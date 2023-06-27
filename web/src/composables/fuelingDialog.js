import { useQuasar } from "quasar";
import FuelingDialog from "src/components/FuelingDialog.vue";

export function useFuelingDialog(context) {
  const $q = useQuasar();

  const showAddFuelingDialog = (vehicleId, vehicleName, priceSetting, title) =>
    $q.dialog({
      component: FuelingDialog,
      componentProps: {
        vehicleId: vehicleId,
        vehicleName: vehicleName,
        priceSetting: priceSetting,
        title: title,
      },
    });

  return { showAddFuelingDialog };
}
