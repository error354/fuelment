import { useQuasar } from "quasar";
import AddFuelingDialog from "src/components/dialogs/AddFuelingDialog.vue";

export function useFuelingDialog() {
  const $q = useQuasar();

  const showAddFuelingDialog = (vehicleId, vehicleName, priceSetting, title) =>
    $q.dialog({
      component: AddFuelingDialog,
      componentProps: {
        vehicleId: vehicleId,
        vehicleName: vehicleName,
        priceSetting: priceSetting,
        title: title,
      },
    });

  return { showAddFuelingDialog };
}
