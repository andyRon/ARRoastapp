export const CafeIsRoasterFilter = {
    methods: {
        processCafeIsRoasterFilter(cafe) {
            // 检查咖啡店是否是烘焙店
            return cafe.roaster === 1;
        }
    }
};
