<template>
    <div class="page">
        <form>
            <div class="grid-container">
                <div class="grid-x grid-padding-x">
                    <div class="large-12 medium-12 small-12 cell">
                        <label>名称
                            <input type="text" placeholder="咖啡店名" v-model="name">
                        </label>
                        <span class="validation" v-show="!validations.name.is_valid">{{ validations.name.text }}</span>
                    </div>
                    <div class="large-12 medium-12 small-12 cell">
                        <label>网址
                            <input type="text" placeholder="网址" v-model="website">
                        </label>
                        <span class="validation" v-show="!validations.website.is_valid">{{ validations.website.text }}</span>
                    </div>
                    <div class="large-12 medium-12 small-12 cell">
                        <label>简介
                            <input type="text" placeholder="简介" v-model="description">
                        </label>
                    </div>

                    <div class="large-12 medium-12 small-12 cell">
                        <label>图片
                            <input type="file" id="cafe-photo" ref="photo" v-on:change="handleFileUpload()"/>
                        </label>
                    </div>

                </div>
                <div class="grid-x grid-padding-x" v-for="(location, key) in locations">
                    <div class="large-12 medium-12 small-12 cell">
                        <h3>位置</h3>
                    </div>
                    <div class="large-6 medium-6 small-12 cell">
                        <label>位置名称
                            <input type="text" placeholder="位置名称" v-model="locations[key].name">
                        </label>
                    </div>
                    <div class="large-6 medium-6 small-12 cell">
                        <label>详细地址
                            <input type="text" placeholder="详细地址" v-model="locations[key].address">
                        </label>
                        <span class="validation" v-show="!validations.locations[key].address.is_valid">{{ validations.locations[key].address.text }}</span>
                    </div>
                    <div class="large-6 medium-6 small-12 cell">
                        <label>城市
                            <input type="text" placeholder="城市" v-model="locations[key].city">
                        </label>
                        <span class="validation" v-show="!validations.locations[key].city.is_valid">{{ validations.locations[key].city.text }}</span>
                    </div>
                    <div class="large-6 medium-6 small-12 cell">
                        <label>省份
                            <input type="text" placeholder="省份" v-model="locations[key].state">
                        </label>
                        <span class="validation" v-show="!validations.locations[key].state.is_valid">{{ validations.locations[key].state.text }}</span>
                    </div>
                    <div class="large-6 medium-6 small-12 cell">
                        <label>邮编
                            <input type="text" placeholder="邮编" v-model="locations[key].zip">
                        </label>
                        <span class="validation" v-show="!validations.locations[key].zip.is_valid">{{ validations.locations[key].zip.text }}</span>
                    </div>
                    <div class="large-12 medium-12 small-12 cell">
                        <label>支持的冲泡方法</label>
                        <span class="brew-method" v-for="brewMethod in brewMethods">
                            <input v-bind:id="'brew-method-'+brewMethod.id+'-'+key" type="checkbox"
                                   v-bind:value="brewMethod.id"
                                   v-model="locations[key].methodsAvailable">
                            <label v-bind:for="'brew-method-'+brewMethod.id+'-'+key">{{ brewMethod.method }}</label>
                        </span>

                        <div class="large-12 medium-12 small-12 cell">
                            <tags-input v-bind:unique="key"></tags-input>
                        </div>

                    </div>
                    <div class="large-12 medium-12 small-12 cell">
                        <a class="button" v-on:click="removeLocation(key)">移除位置</a>
                    </div>
                </div>
                <div class="grid-x grid-padding-x">
                    <div class="large-12 medium-12 small-12 cell">
                        <a class="button" v-on:click="addLocation()">新增位置</a>
                    </div>
                    <div class="large-12 medium-12 small-12 cell">
                        <a class="button" v-on:click="submitNewCafe()">提交表单</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

</template>

<script>
import TagsInput from '../components/global/forms/TagsInput.vue';
import { EventBus } from '../event-bus.js';

export default {
    components: {
        TagsInput
    },
    data() {
        return {
            name: '',
            locations: [],
            website: '',
            description: '',
            roaster: false,
            validations: {
                name: {
                    is_valid: true,
                    text: ''
                },
                locations: [],
                oneLocation: {  // 确保咖啡店至少包含一个位置信息
                    is_valid: true,
                    text: ''
                },
                website: {
                    is_valid: true,
                    text: ''
                }
            }
        }
    },
    created() {
      this.addLocation();
    },
    methods: {
        submitNewCafe: function () {
            if (this.validateNewCafe()) {
                this.$store.dispatch('addCafe', {
                    name: this.name,
                    locations: this.locations,
                    website: this.website,
                    description: this.description,
                    roaster: this.roaster,
                    picture: this.picture
                });
            }
        },
        validateNewCafe: function () {
            let validNewCafeForm = true;

            // 确保 name 字段不为空
            if( this.name.trim() === '' ) {
                validNewCafeForm = false;
                this.validations.name.is_valid = false;
                this.validations.name.text = '请输入咖啡店的名字';
            } else {
                this.validations.name.is_valid = true;
                this.validations.name.text = '';
            }

            // 确保 address 字段不为空
            if( this.address.trim() === '' ) {
                validNewCafeForm = false;
                this.validations.address.is_valid = false;
                this.validations.address.text = '请输入咖啡店的地址!';
            } else {
                this.validations.address.is_valid = true;
                this.validations.address.text = '';
            }

            //  确保 city 字段不为空
            if( this.city.trim() === '' ) {
                validNewCafeForm = false;
                this.validations.city.is_valid = false;
                this.validations.city.text = '请输入咖啡店所在城市!';
            } else {
                this.validations.city.is_valid = true;
                this.validations.city.text = '';
            }

            //  确保 state 字段不为空
            if( this.state.trim() === '' ) {
                validNewCafeForm = false;
                this.validations.state.is_valid = false;
                this.validations.state.text = '请输入咖啡店所在省份!';
            } else {
                this.validations.state.is_valid = true;
                this.validations.state.text = '';
            }

            // 确保 zip 字段不为空且格式正确
            if( this.zip.trim() === '' || !this.zip.match(/(^\d{6}$)/) ) {
                validNewCafeForm = false;
                this.validations.zip.is_valid = false;
                this.validations.zip.text = '请输入有效的邮编地址!';
            } else {
                this.validations.zip.is_valid = true;
                this.validations.zip.text = '';
            }

            // 确保网址是有效的URL
            if (this.website.trim() !== '' && !this.website.match(/^((https?):\/\/)?([w|W]{3}\.)?[a-zA-Z0-9\-\.]{3,}\.[a-zA-Z]{2,}(\.[a-zA-Z]{2,})?$/)) {
                validNewCafeForm = false;
                this.validations.website.is_valid = false;
                this.validations.website.text = '请输入有效的咖啡店网址';
            } else {
                this.validations.website.is_valid = true;
                this.validations.website.text = '';
            }

            return validNewCafeForm;
        },
        addLocation: function () {  // 用于新增一个位置区块到表单中，并在组件创建后进行调用
            this.locations.push({name: '', address: '', city: '', state: '', zip: '', methodsAvailable: [], tags: ''});
            this.validations.locations.push({
               address: {
                   is_valid: true,
                   text: ''
               },
                city: {
                    is_valid: true,
                    text: ''
                },
                state: {
                    is_valid: true,
                    text: ''
                },
                zip: {
                    is_valid: true,
                    text: ''
                }
            });
        },
        removeLocation(key) {
            this.locations.splice(key, 1);
            this.validations.locations.splice(key, 1);
        },
        clearForm() { // 重置表单数据
            this.name = '';
            this.locations = [];
            this.website = '';
            this.description = '';
            this.roaster = false;
            this.picture = '';
            this.$refs.photo.value = '';

            this.validations = {
                name: {
                    is_valid: true,
                    text: ''
                },
                locations: [],
                oneLocation: {
                    is_valid: true,
                    text: ''
                },
                website: {
                    is_valid: true,
                    text: ''
                }
            };

            // TODO  清理标签输入框的值
            EventBus.$emit('clear-tags');

            // 清理完表单数据信息后，添加一个新的位置信息到表单
            this.addLocation();
        },

    },
    computed: {
        brewMethods() {
            return this.$store.getters.getBrewMethods();
        },
        addCafeStatus() {
            return this.$store.getters.getCafeAddStatus;
        }
    },
    watch: {  // TODO
        'addCafeStatus': function () {
            if (this.addCafeStatus === 2) {  // 添加成功
                this.clearForm();
                $("#cafe-added-successfully").show().delay(5000).fadeOut();
            }
            if (this.addCafeStatus === 3) {  // 添加失败
                $("#cafe-added-unsuccessfully").show().delay(5000).fadeOut();
            }
        }
    },
    mounted() {
        EventBus.$on('tags-edited', function (tagsAdded) {
            this.locations[tagsAdded.unique].tags = tagsAdded.tags;
        }.bind(this));
    }
}
</script>

<style>

</style>
