<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditCafeRequest;
use App\Http\Requests\StoreCafeRequest;
use App\Models\Cafe;
use App\Models\Company;
use App\Services\ActionService;
use App\Services\CafeService;
use App\Utilities\GaodeMaps;
use App\Utilities\Tagger;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class CafesController extends Controller
{
    public function getCafes()
    {
        $cafes = Cafe::query()->with('brewMethods')
            ->with(['tags' => function ($query) {
                $query->select('tag');
            }])
            ->with('company')
            ->withCount('userLike')
            ->withCount('likes')
            ->get();
        return response()->json($cafes);
    }

    public function getCafe($id)
    {
        $cafe = Cafe::query()->where('id', '=', $id)
            ->with('brewMethods')
            ->with('userLike')
            ->with('tags')
            ->with(['company' => function ($query) {
                $query->withCount('cafes');
            }])
            ->withCount('likes')
            ->first();
        return response()->json($cafe);
    }

    public function postNewCafe(StoreCafeRequest $request): JsonResponse
    {
        // 已添加的咖啡店
        $addedCafes = [];
        // 所有位置信息
        $locations = $request->input('locations');

        // 父节点（可理解为总店）
        $parentCafe = new Cafe();

        // 咖啡店名称
        $parentCafe->name = $request->input('name');
        // 分店位置名称
        $parentCafe->location_name = $locations[0]['name'] ?: '';
        // 分店地址
        $parentCafe->address = $locations[0]['address'];
        // 所在城市
        $parentCafe->city = $locations[0]['city'];
        // 所在省份
        $parentCafe->state = $locations[0]['state'];
        // 邮政编码
        $parentCafe->zip = $locations[0]['zip'];
        $coordinates = GaodeMaps::geocodeAddress($parentCafe->address, $parentCafe->city, $parentCafe->state);
        // 纬度
        $parentCafe->latitude = $coordinates['lat'];
        // 经度
        $parentCafe->longitude = $coordinates['lng'];
        // 咖啡烘焙师
        $parentCafe->roaster = $request->input('roaster') ? 1 : 0;
        // 咖啡店网址
        $parentCafe->website = $request->input('website');
        // 描述信息
        $parentCafe->description = $request->input('description') ?: '';
        // 添加者
        $parentCafe->added_by = $request->user()->id;
        $parentCafe->save();

        // 冲泡方法
        $brewMethods = $locations[0]['methodsAvailable'];
        // 保存与此咖啡店关联的所有冲泡方法（保存关联关系）
        $parentCafe->brewMethods()->sync($brewMethods);

        $tags = $locations[0]['tags'];
        Tagger::tagCafe($parentCafe, $tags, $request->user()->id);

        // 将当前咖啡店数据推送到已添加咖啡店数组
        array_push($addedCafes, $parentCafe->toArray());

        // 第一个索引的位置信息已经使用，从第 2 个位置开始
        if (count($locations) > 1) {
            // 从索引值 1 开始，以为第一个位置已经使用了
            for ($i = 1; $i < count($locations); $i++) {
                // 其它分店信息的获取和保存，与总店共用名称、网址、描述、烘焙师等信息，其他逻辑与总店一致
                $cafe = new Cafe();

                $cafe->parent = $parentCafe->id;
                $cafe->name = $request->input('name');
                $cafe->location_name = $locations[$i]['name'] ?: '';
                $cafe->address = $locations[$i]['address'];
                $cafe->city = $locations[$i]['city'];
                $cafe->state = $locations[$i]['state'];
                $cafe->zip = $locations[$i]['zip'];
                $coordinates = GaodeMaps::geocodeAddress($cafe->address, $cafe->city, $cafe->state);
                $cafe->latitude = $coordinates['lat'];
                $cafe->longitude = $coordinates['lng'];
                $cafe->roaster = $request->input('roaster') != '' ? 1 : 0;
                $cafe->website = $request->input('website');
                $cafe->description = $request->input('description') ?: '';
                $cafe->added_by = $request->user()->id;
                $cafe->save();

                $cafe->brewMethods()->sync($locations[$i]['methodsAvailable']);

                Tagger::tagCafe($cafe, $locations[$i]['tags'], $request->user()->id);

                array_push($addedCafes, $cafe->toArray());
            }
        }

        return response()->json($addedCafes, 201);
    }
    public function postNewCafe2(StoreCafeRequest $request)
    {
        $companyID = $request->input('company_id');
        $company = Company::where('id', '=', $companyID)->first();
        $company = $company == null ? new Company() : $company;

        $actionService = new ActionService();
        if (Auth::user()->can('create', [Cafe::class, $company])) {
            $cafeService = new CafeService();
            $cafe = $cafeService->addCafe($request->all(), Auth::user()->id);

            $actionService->createApprovedAction(null, $cafe->company_id, 'cafe-added', $request->all(), Auth::user()->id);

            $company = Company::where('id', '=', $cafe->company_id)
                ->with('cafes')
                ->first();

            return response()->json($company, 201);
        } else {
            $actionService->createPendingAction(null, $request->get('company_id'), 'cafe-added', $request->all(), Auth::user()->id);
            return response()->json(['cafe_add_pending' => $request->get('company_name')], 202);
        }
    }

    /**
     * 喜欢咖啡店
     * @param $cafeId
     * @return JsonResponse
     */
    public function postLikeCafe($cafeId)
    {
        $cafe = Cafe::query()->where('id', '=', $cafeId)->first();
        $cafe->likes()->attach(Auth::user()->id, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        return response()->json(['cafe_liked' => true], 201);
    }

    /**
     * 取消喜欢咖啡店
     * @param $cafeID
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function deleteLikeCafe($cafeID)
    {
        $cafe = Cafe::query()->where('id', '=', $cafeID)->first();

        $cafe->likes()->detach(Auth::user()->id);

        return response(null, 204);
    }

    /**
     * 给咖啡店添加标签
     * @param Request $request
     * @param $cafeID
     * @return JsonResponse
     */
    public function postAddTags(Request $request, $cafeID)
    {
        $tags = $request->input('tags');
        $cafe = Cafe::query()->find($cafeID);

        Tagger::tagCafe($cafe, $tags, Auth::user()->id);

        $cafe = Cafe::query()->where('id', '=', $cafeID)
            ->with('brewMethods')
            ->with('userLike')
            ->with('tags')
            ->first();

        return response()->json($cafe, 201);
    }

    /**
     * 删除咖啡店上的指定标签
     * @param $cafeID
     * @param $tagID
     * @return Response
     */
    public function deleteCafeTag($cafeID, $tagID)
    {
        DB::table('cafes_users_tags')->where('cafe_id', $cafeID)
            ->where('tag_id', $tagID)
            ->where('user_id', Auth::user()->id)
            ->delete();
        return response(null, 204);
    }

    /**
     * 更新咖啡店数据
     * @param $id
     * @param EditCafeRequest $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function putEditCafe($id, EditCafeRequest $request)
    {
        $cafe = Cafe::where('id', '=', $id)->with('brewMethods')->with('company')->first();
        if (!$cafe) {
            abort(404);
        }

        // 保存修改之前/之后的咖啡店数据
        $content['before'] = $cafe;
        $content['after'] = $request->all();

        $actionService = new ActionService();
        if (Auth::user()->can('update', $cafe)) {
            // 具备更新权限自动审核通过
            $actionService->createApprovedAction($cafe->id, $cafe->company_id, 'cafe-updated', $content, Auth::user()->id);
            $cafeService = new CafeService();
            $updatedCafe = $cafeService->editCafe($cafe->id, $request->all(), Auth::user()->id);

            $company = Company::where('id', '=', $updatedCafe->company_id)
                ->with('cafes')
                ->first();

            return response()->json($company, 200);
        } else {
            // 不具备更新权限需要等待后台审核通过才能更新这个咖啡店
            $actionService->createPendingAction($cafe->id, $cafe->company_id, 'cafe-updated', $content, Auth::user()->id);
            return response()->json(['cafe_updates_pending' => $request->get('company_name')], 202);
        }
    }

    /**
     * 删除咖啡店
     * @param $id
     * @return JsonResponse
     */
    public function deleteCafe($id)
    {
        $cafe = Cafe::where('id', '=', $id)->with('company')->first();
        if (!$cafe) {
            abort(404);
        }

        $actionService = new ActionService();
        if (Auth::user()->can('delete', $cafe)) {
            // 具备删除权限自动审核通过
            $actionService->createApprovedAction($cafe->id, $cafe->company_id, 'cafe-deleted', '', Auth::user()->id);

            $cafe->delete();
            return response()->json(['message' => '删除成功'], 204);
        } else {
            // 不具备删除权限需要等后台审核通过后才能执行删除操作
            $actionService->createPendingAction($cafe->id, $cafe->company_id, 'cafe-deleted', '', Auth::user()->id);
            return response()->json(['cafe_delete_pending' => $cafe->company->name], 202);
        }
    }
}
