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
          :loading="emitting"
          @click="onDialogOK"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { ref } from "vue";
import { useDialogPluginComponent } from "quasar";
import { i18n } from "src/boot/i18n";

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
  setup(props, { emit }) {
    const { dialogRef, onDialogHide, onDialogCancel } =
      useDialogPluginComponent();

    const emitting = ref(false);

    const onDialogOK = async () => {
      emitting.value = true;
      emit("ok", {
        done: () => {
          onDialogCancel();
          emitting.value = false;
        },
      });
    };

    return {
      dialogRef,
      onDialogHide,
      onDialogOK,
      onDialogCancel,
      emitting,
    };
  },
};
</script>
