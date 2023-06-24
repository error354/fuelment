<template>
  <q-layout view="hHh Lpr fFf">
    <q-header class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          round
          icon="mdi-menu"
          class="lt-md"
          @click="showDrawer = !showDrawer"
        />
        <q-toolbar-title>
          <router-link to="/">
            <q-img
              no-spinner
              no-native-menu
              src="~src/assets/logo_without_background.svg"
              height="1.8em"
              width="7em"
              fit="contain"
            />
          </router-link>
        </q-toolbar-title>
        <q-btn
          class="gt-sm"
          flat
          stretch
          icon-right="mdi-home-outline"
          to="/"
          :label="$t('header.home')"
        />
        <q-btn
          class="gt-sm"
          flat
          stretch
          icon-right="mdi-chart-line"
          :label="$t('header.stats')"
        />
        <q-btn-dropdown
          class="gt-sm"
          flat
          stretch
          no-icon-animation
          dropdown-icon="mdi-cog-outline"
          content-class="no-border-radius"
          :label="$t('header.administration')"
          v-if="hasAnyAdminViewPermission()"
        >
          <q-list>
            <q-item
              clickable
              v-close-popup
              v-if="hasPermission(adminViewPermissions.vehicles)"
            >
              <q-item-section>
                <q-item-label>{{ $t("header.vehicles") }}</q-item-label>
              </q-item-section>
            </q-item>

            <q-item
              clickable
              v-close-popup
              v-if="hasPermission(adminViewPermissions.users)"
            >
              <q-item-section>
                <q-item-label>{{ $t("header.users") }}</q-item-label>
              </q-item-section>
            </q-item>

            <q-item
              clickable
              v-close-popup
              v-if="hasPermission(adminViewPermissions.roles)"
            >
              <q-item-section>
                <q-item-label>{{ $t("header.roles") }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn
          class="gt-sm"
          flat
          stretch
          icon-right="mdi-account-outline"
          :label="$t('header.settings')"
        >
          <q-tooltip> {{ email }} </q-tooltip>
        </q-btn>
        <q-btn
          class="gt-sm"
          flat
          stretch
          icon-right="mdi-power"
          :loading="logoutLoading"
          @click="logout"
          :label="$t('header.logout')"
        />
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="showDrawer"
      width="200"
      overlay
      behavior="mobile"
      class="bg-secondary text-white lt-md"
    >
      <q-scroll-area class="fit">
        <div class="text-center full-width q-my-md">
          {{ email }}
        </div>
        <q-separator spaced dark />
        <q-list class="text-uppercase">
          <drawer-button
            icon="mdi-home-outline"
            :label="$t('header.home')"
            to="/"
          />
          <drawer-button
            icon="mdi-chart-line"
            :label="$t('header.stats')"
            to="/stats"
          />
          <drawer-button
            icon="mdi-account-outline"
            :label="$t('header.settings')"
            to="/settings"
          />
          <q-separator spaced dark />
          <drawer-button
            icon="mdi-car-outline"
            :label="$t('header.vehicles')"
            v-if="hasPermission(adminViewPermissions.vehicles)"
            to="/vehicles"
          />
          <drawer-button
            icon="mdi-account-multiple-outline"
            :label="$t('header.users')"
            v-if="hasPermission(adminViewPermissions.users)"
            to="/users"
          />
          <drawer-button
            icon="mdi-shield-key-outline"
            :label="$t('header.roles')"
            v-if="hasPermission(adminViewPermissions.roles)"
            to="/roles"
          />
          <q-separator spaced dark v-if="hasAnyAdminViewPermission()" />
          <drawer-button
            icon="mdi-power"
            :label="$t('header.logout')"
            :loading="logoutLoading"
            @click="logout"
          />
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useProfileStore } from "../stores/profile.js";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import DrawerButton from "src/components/DrawerButton.vue";

export default defineComponent({
  name: "MainLayout",
  components: { DrawerButton },
  setup() {
    const router = useRouter();
    const profileStore = useProfileStore();
    const { email } = storeToRefs(profileStore);

    const logoutLoading = ref(false);
    async function logout() {
      logoutLoading.value = true;
      const logoutResponse = await profileStore.logout();
      if (!logoutResponse.error) {
        router.push("/login");
      }
      logoutLoading.value = false;
    }

    const showDrawer = ref(false);

    const adminViewPermissions = ref({
      vehicles: "vehicles.view",
      users: "users.view",
      roles: "roles.view",
    });

    function hasAnyAdminViewPermission() {
      return (
        profileStore.hasPermission(adminViewPermissions.value.vehicles) ||
        profileStore.hasPermission(adminViewPermissions.value.users) ||
        profileStore.hasPermission(adminViewPermissions.value.roles)
      );
    }

    return {
      logout,
      logoutLoading,
      email,
      showDrawer,
      hasPermission: profileStore.hasPermission,
      adminViewPermissions,
      hasAnyAdminViewPermission,
    };
  },
});
</script>
