<template>
  <q-form greedy @submit="login">
    <email-input
      v-model="credentials.email"
      :label="$t('login.emailAddress')"
      :rules="[$rules.required($t('validation.required'))]"
    />
    <password-input
      v-model="credentials.password"
      :label="$t('login.password')"
      :rules="[$rules.required($t('validation.required'))]"
    />
    <auth-button :label="$t('login.loginButton')" :loading="loadingLogin" />
    <div class="row full-width justify-center">
      <router-link class="auth-link" to="/forgot-password">{{
        $t("login.forgotPassword")
      }}</router-link>
    </div>
  </q-form>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useProfileStore } from "../stores/profile.js";
import { useRouter } from "vue-router";
import AuthButton from "../components/buttons/AuthButton.vue";
import PasswordInput from "../components/inputs/PasswordInput.vue";
import EmailInput from "../components/inputs/EmailInput.vue";

export default defineComponent({
  name: "LoginPage",
  components: { AuthButton, PasswordInput, EmailInput },
  setup() {
    const profileStore = useProfileStore();
    const router = useRouter();
    const credentials = ref({
      email: "",
      password: "",
    });
    const loadingLogin = ref(false);
    async function login() {
      loadingLogin.value = true;
      const loginResponse = await profileStore.login(
        credentials.value.email,
        credentials.value.password
      );
      let profileResponse;
      if (!loginResponse.error) {
        profileResponse = await profileStore.fetchProfile();
      }
      if (profileResponse) {
        router.push("/");
      }
      loadingLogin.value = false;
    }
    return {
      login,
      credentials,
      loadingLogin,
    };
  },
});
</script>

<style></style>
