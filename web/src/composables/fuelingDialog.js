import { useQuasar } from "quasar";
import AddFuelingDialog from "src/components/AddFuelingDialog.vue";

export function useFuelingDialog(context) {
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
