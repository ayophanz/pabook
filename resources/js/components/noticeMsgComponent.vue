<template>
    <div>
        <div class="parent el-loading-notice-msg">
            <vue-element-loading :active="$store.getters.trigLoaderNoticeGett" spinner="bar-fade-scale" color="#38d39f"/>
            <div style="height:100px;width:100%;margin-bottom:10px;background-color:transparent !important;"></div>
        </div>
        <flash-message class="notifybar-msg notif-warning"></flash-message>
    </div>
</template>