<template>
    <div>
        <div :class="{ 'hide' : $store.getters.trigLoaderNoticeGett==false }" class="parent el-loading-notice-msg">
            <vue-element-loading :active="$store.getters.trigLoaderNoticeGett" spinner="bar-fade-scale" color="#38d39f"/>
            <div style="height:100px;width:100%;margin-bottom:10px;background-color:transparent !important;"></div>
        </div>
        <flash-message class="notifybar-msg notif-warning"></flash-message>
    </div>
</template>
<style lang='scss'>
    .warning.flash__message {
        border-left: 5px solid #fd7e14 !important;
    }
    .el-loading-notice-msg.hide {
        display: none;
    }
</style>