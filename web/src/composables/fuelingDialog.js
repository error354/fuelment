import { useQuasar } from "quasar";
import FuelingDialog from "src/components/FuelingDialog.vue";

export function useFuelingDialog(context) {
  const $q = useQuasar();

  const showPrice = (priceSetting) => {
    if (priceSetting == "DISABLED") {
      return false;
    }
    return true;
  };

  const showAddFuelingDialog = (vehicleId, vehicleName, showPrice, title) =>
    $q.dialog({
      component: FuelingDialog,
      componentProps: {
        vehicleId: vehicleId,
        vehicleName: vehicleName,
        title: title,
        showPrice: showPrice,
      },
    });

  return { showPrice, showAddFuelingDialog };
}
