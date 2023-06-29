<template>
  <q-dialog ref="dialogRef" no-backdrop-dismiss @hide="onDialogHide">
    <q-card class="q-dialog-plugin">
      <q-card-section>
        <div class="text-h6" v-if="title">{{ title }}</div>
        <div class="text-h6" v-if="subtitle">{{ subtitle }}</div>
      </q-card-section>
      <q-card-section>
        <slot>
          {{ content }}
        </slot>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          :color="cancelColor"
          flat
          :label="cancelLabel"
          @click="onDialogCancel"
        />
        <q-btn
          :color="confirmColor"
          flat
          :label="confirmText"
          :loading="loading"
          @click="onDialogOK"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { useDialogPluginComponent } from "quasar";
import { i18n } from "../boot/i18n";

const $t = i18n.global.t;
export default {
  props: {
    title: String,
    subtitle: String,
    confirmColor: {
      type: String,
      default: "primary",
    },
    confirmText: {
      type: String,
      default: $t("confirmationDialog.confirm"),
    },
    cancelColor: {
      type: String,
      default: "primary",
    },
    cancelLabel: {
      type: String,
      default: $t("confirmationDialog.cancel"),
    },
    content: String,
  },
  emits: [...useDialogPluginComponent.emits],
  setup() {
    const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } =
      useDialogPluginComponent();

    return {
      dialogRef,
      onDialogHide,
      onDialogOK,
      onDialogCancel,
    };
  },
};
</script>
