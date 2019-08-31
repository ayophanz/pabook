<template>
    <div id="badge">
      <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
        <i class="fas fa-bell fa-2x"></i>
        <span :class="displayy" class="display-none badge badge-danger navbar-badge">{{unreadNotifications.length}}</span>
      </a>
    </div>
</template>
<script>
    export default {
      data() {
        return {
          allNotifications: [],
          displayy: 'display-none'
        }
      },
      methods: {
        reloadNotification() {
          if (this.$gate.superAdminOrhotelOwnerOrhotelReceptionist()) {
              let self = this
              axios.get('/api/mark-as-read')
              .then(
                  function (response) {
                      self.allNotifications = response.data.notifications;
                  }
              );
          }
        }
      },
      computed: {
        unreadNotifications() {
          return this.allNotifications.filter(notification=>{
            return notification.read_at == null;
          })
        }
      },
      created() {
        fire.$on('loadCounterNotify', this.reloadNotification);
        this.displayy = 'display-inline-table';

      }
    }
</script>

<style lang='scss'>
    #badge .badge-danger {
        color: #fff;
        background-color: #e3342f;
        padding: 5px 7px;
    }

    .display-none {
        display: none !important;
    } 

    .display-inline-table {
        display: inline-table !important;
    } 
</style>