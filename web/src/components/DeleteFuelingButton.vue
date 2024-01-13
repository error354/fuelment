<template>
  <q-btn
    round
    flat
    color="primary"
    icon="mdi-delete"
    size="xs"
    @click="showConfirmDialog"
  >
    <q-tooltip>{{ $t("fuelingsTable.delete") }}</q-tooltip>
  </q-btn>
</template>

<script>
import { defineComponent } from "vue";
import { useQuasar } from "quasar";
import { i18n } from "../boot/i18n";
import { apiClient, handleErrors } from "src/boot/apiClient";
import ConfirmDialog from "src/components/ConfirmDialog.vue";

const $t = i18n.global.t;
export default defineComponent({
  props: {
    vehicleId: {
      type: Number,
      reqired: true,
    },
    fuelingId: {
      type: Number,
      reqired: true,
    },
  },
  emits: ["fuelingChanged"],
  setup(props, { emit }) {
    const $q = useQuasar();

    const showConfirmDialog = async () =>
      $q
        .dialog({
          component: ConfirmDialog,
          componentProps: {
            title: $t("fuelingsTable.deletingFueling.title"),
            content: $t("fuelingsTable.deletingFueling.content"),
            confirmColor: "negative",
          },
        })
        .onOk(async ({ done }) => {
          await deleteFueling();
          emit("fuelingChanged");
          done();
        });

    const deleteFuelingMutation = `
      mutation deleteFueling($fuelingId: [ID!]!) {
        deleteFueling(
          id: $fuelingId
        ) {
          id
        }
      }
    `;
    async function deleteFueling() {
      await apiClient
        .executeMutation({
          query: deleteFuelingMutation,
          variables: {
            fuelingId: props.fuelingId,
          },
          clearCacheTags: [`vehicle_${props.vehicleId}_fuelings`],
        })
        .then((response) => {
          if (response.error || response.errors) {
            handleErrors(response.error);
          }
        });
    }

    return { showConfirmDialog };
  },
});
</script>
