<template>
  <q-form greedy @submit="resetPassword">
    <password-input
      v-model="password1"
      :label="$t('resetPassword.newPassword1')"
      :rules="[$rules.required($t('validation.required')), $rules.minLength(8)]"
    />
    <password-input
      v-model="password2"
      :label="$t('resetPassword.newPassword2')"
      :rules="[
        $rules.required($t('validation.required')),
        $rules.minLength(8),
        $rules.sameAs(password1, $t('resetPassword.passwordsDiffer')),
      ]"
    />
    <auth-button
      :label="$t('resetPassword.setNewPassword')"
      :loading="loadingResetPassword"
    />
  </q-form>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useProfileStore } from "../stores/profile.js";
import { useRouter, useRoute } from "vue-router";
import { useQuasar } from "quasar";
import { useI18n } from "vue-i18n";
import AuthButton from "../components/AuthButton.vue";
import PasswordInput from "../components/PasswordInput.vue";

export default defineComponent({
  name: "ResetPasswordPage",
  components: { AuthButton, PasswordInput },
  setup() {
    const profileStore = useProfileStore();
    const router = useRouter();
    const route = useRoute();
    const $q = useQuasar();
    const { t } = useI18n();

    const password1 = ref("");
    const password2 = ref("");
    const loadingResetPassword = ref(false);

    async function resetPassword() {
      loadingResetPassword.value = true;
      const resetPasswordResponse = await profileStore.resetPassword(
        route.query.email,
        route.query.token,
        password1.value
      );
      if (!resetPasswordResponse.error) {
        router.push("/login");
        $q.notify(t("resetPassword.done"));
      }
      loadingResetPassword.value = false;
    }

    return {
      resetPassword,
      password1,
      password2,
      loadingResetPassword,
    };
  },
});
</script>

<style></style>
