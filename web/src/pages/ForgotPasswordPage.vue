<template>
  <q-form greedy @submit="forgotPassword">
    <email-input
      v-model="email"
      :label="$t('login.emailAddress')"
      :rules="[$rules.required($t('validation.required'))]"
    />
    <auth-button
      :label="$t('forgotPassword.forgotPassword')"
      :loading="loadingForgotPassword"
    />
    <div class="row full-width justify-center">
      <router-link class="auth-link" to="/login">{{
        $t("forgotPassword.returnToLogin")
      }}</router-link>
    </div>
  </q-form>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useProfileStore } from "../stores/profile.js";
import { useQuasar } from "quasar";
import { useI18n } from "vue-i18n";
import AuthButton from "../components/AuthButton.vue";
import EmailInput from "../components/EmailInput.vue";

export default defineComponent({
  name: "ForgotPasswordPage",
  components: { AuthButton, EmailInput },
  setup() {
    const $q = useQuasar();
    const { t } = useI18n();
    const profileStore = useProfileStore();

    const email = ref("");

    const loadingForgotPassword = ref(false);

    async function forgotPassword() {
      loadingForgotPassword.value = true;
      const forgotPasswordResponse = await profileStore.forgotPassword(
        email.value
      );
      if (!forgotPasswordResponse.error) {
        $q.notify(t("forgotPassword.sent"));
      }
      loadingForgotPassword.value = false;
    }

    return {
      forgotPassword,
      email,
      loadingForgotPassword,
    };
  },
});
</script>

<style></style>
