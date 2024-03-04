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
          :label="cancelLabelWithDefault"
          @click="onDialogCancel"
        />
        <q-btn
          :color="confirmColor"
          flat
          :label="confirmTextWithDefault"
          :loading="emitting"
          @click="onDialogOK"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, computed } from "vue";
import { useDialogPluginComponent } from "quasar";
import { i18n } from "src/boot/i18n";

const $t = i18n.global.t;

const props = defineProps({
  title: String,
  subtitle: String,
  confirmColor: {
    type: String,
    default: "primary",
  },
  confirmText: {
    type: String,
    default: "",
  },
  cancelColor: {
    type: String,
    default: "primary",
  },
  cancelLabel: {
    type: String,
    default: "",
  },
  content: String,
});
const confirmTextWithDefault = computed(() => {
  return props.confirmText || $t("confirmationDialog.confirm");
});
const cancelLabelWithDefault = computed(() => {
  return props.cancelLabel || $t("confirmationDialog.cancel");
});
const emit = defineEmits([...useDialogPluginComponent.emits]);
const { dialogRef, onDialogHide, onDialogCancel } = useDialogPluginComponent();

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
</script>
