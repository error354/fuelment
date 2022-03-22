<template>
  <div class="fullscreen bg-image q-pa-md no-padding scroll">
    <div class="row items-start justify-start full-height">
      <div
        class="col-md-3 col-xs-10 offset-md-2 offset-xs-1 auth-form shadow-5 q-px-xl q-pb-xl"
      >
        <div class="column full-height justify-center">
          <div class="col row items-center full-height">
            <div class="col">
              <q-img
                no-spinner
                no-native-menu
                src="src/assets/logo_with_background.svg"
                class="full-width"
              />
            </div>
          </div>
          <div class="col column justify-end">
            <router-view v-slot="{ Component }">
              <transition
                enter-active-class="animated fadeIn"
                leave-active-class="animated fadeOut"
                mode="out-in"
              >
                <component :is="Component" />
              </transition>
            </router-view>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  name: "AuthLayout",
});
</script>

<style>
.bg-image {
  background-image: url("src/assets/background.jpg");
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

.auth-form {
  height: 70% !important;
  min-height: 510px;
  background-color: #fff;
}
</style>
