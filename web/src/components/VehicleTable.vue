<template>
  <q-table
    square
    flat
    class="col-grow fuelings-table"
    :title="vehicle.name"
    :loading="loadingFuelings"
    :columns="columns"
    :rows="fuelings"
    :visible-columns="visibleColumns"
    :no-data-label="$t('fuelingsTable.empty')"
    v-model:pagination="paginationProps.pagination"
    rows-per-page-options="5"
    icon-first-page="first_page"
    icon-last-page="last_page"
    @request="getFuelings"
  >
    <template v-slot:top>
      <div
        class="relative-position row items-center handle cursor-grab full-width"
      >
        <div>
          <q-icon
            name="mdi-circle-medium"
            class="cursor-default"
            size="md"
            :color="color"
          >
            <q-tooltip self="center middle">
              {{ $t(`fuelingsTable.fuel.${vehicle.fuel}`) }}
            </q-tooltip>
          </q-icon>
        </div>
        <span class="text-h6">{{ vehicle.name }}</span>
        <q-space />
        <span class="q-mr-sm" v-if="vehicle.avgFuelConsumption">
          {{
            `${$t("fuelingsTable.avgFuelConsumption")}: ${
              vehicle.avgFuelConsumption
            }`
          }}
        </span>
        <q-btn
          round
          flat
          color="secondary"
          icon="add"
          size="md"
          v-if="vehicle.canAdd"
          @click="
            showAddFuelingDialog(vehicle.name, showPrice(vehicle.priceSetting))
          "
        >
          <q-tooltip self="center middle">{{
            $t("fuelingsTable.add")
          }}</q-tooltip>
        </q-btn>
        <q-btn round flat color="primary" icon="read_more" size="md">
          <q-tooltip self="center middle">{{
            $t("fuelingsTable.details")
          }}</q-tooltip>
        </q-btn>
      </div>
    </template>
    <template v-slot:body-cell-full="props">
      <q-td :props="props">
        <q-icon :name="props.value ? 'mdi-check' : 'mdi-close'"></q-icon>
      </q-td>
    </template>
    <template v-slot:body-cell-totalPrice="props">
      <q-td :props="props">
        {{ totalPrice(props.row.price, props.row.amount) }}
      </q-td>
    </template>
    <template v-slot:body-cell-actions="props">
      <q-td :props="props" class="row justify-center content-center">
        <q-btn
          round
          flat
          color="primary"
          icon="mdi-pencil"
          size="xs"
          v-if="vehicle.canEdit"
          @click="
            showEditFuelingDialog(
              vehicle.name,
              props.row.date,
              props.row.mileage,
              props.row.amount,
              props.row.full,
              showPrice(vehicle.priceSetting),
              props.row.price
            )
          "
        >
          <q-tooltip>{{ $t("fuelingsTable.edit") }}</q-tooltip>
        </q-btn>
        <q-btn
          round
          flat
          color="primary"
          icon="mdi-delete"
          size="xs"
          v-if="vehicle.canDelete"
        >
          <q-tooltip>{{ $t("fuelingsTable.delete") }}</q-tooltip>
        </q-btn>
      </q-td>
    </template>
  </q-table>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useQuasar } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { i18n } from "../boot/i18n";
import FuelingDialog from "src/components/FuelingDialog.vue";

const $t = i18n.global.t;

export default defineComponent({
  name: "VehicleTable",
  props: {
    vehicle: Object,
  },
  setup(props) {
    const $q = useQuasar();

    const showPrice = (priceSetting) => {
      if (priceSetting == "DISABLED") {
        return false;
      }
      return true;
    };

    const showAddFuelingDialog = (vehicleName, showPrice) =>
      $q
        .dialog({
          component: FuelingDialog,
          componentProps: {
            vehicleName: vehicleName,
            title: $t("fuelingsTable.addingDialogTitle"),
            showPrice: showPrice,
          },
        })
        .onOk(() => {
          console.log("OK");
        })
        .onCancel(() => {
          console.log("Cancel");
        })
        .onDismiss(() => {
          console.log("Called on OK or Cancel");
        });

    const showEditFuelingDialog = (
      vehicleName,
      date,
      mileage,
      amount,
      full,
      showPrice,
      price
    ) => {
      console.log(date);
      $q.dialog({
        component: FuelingDialog,
        componentProps: {
          vehicleName: vehicleName,
          title: $t("fuelingsTable.editingDialogTitle"),
          date: date,
          mileage: mileage,
          amount: amount,
          price: price,
          full: full,
          showPrice: showPrice,
        },
      })
        .onOk(() => {
          console.log("OK");
        })
        .onCancel(() => {
          console.log("Cancel");
        })
        .onDismiss(() => {
          console.log("Called on OK or Cancel");
        });
    };

    let color = ref("blue-grey");
    switch (props.vehicle.fuel) {
      case "DIESEL":
        color = "dark";
        break;
      case "PETROL":
        color = "green";
        break;
      case "LPG":
        color = "amber";
        break;
      case "CNG":
        color = "brown";
        break;
      case "HYDROGEN":
        color = "light-blue";
        break;
      case "ELECTRICITY":
        color = "yellow";
        break;
      default:
        color = "blue-grey";
        break;
    }

    const columns = ref([
      {
        name: "date",
        label: $t("fuelingsTable.date"),
        field: "date",
        required: true,
      },
      {
        name: "mileage",
        label: $t("fuelingsTable.mileage"),
        field: "mileage",
        required: true,
      },
      {
        name: "amount",
        label: $t("fuelingsTable.amount"),
        field: "amount",
        required: true,
      },
      {
        name: "full",
        label: $t("fuelingsTable.full"),
        field: "full",
        required: true,
        align: "center",
      },
      {
        name: "fuelConsumption",
        label: $t("fuelingsTable.fuelConsumption"),
        field: "fuelConsumption",
        required: true,
      },
      {
        name: "price",
        label: $t("fuelingsTable.price"),
        field: "price",
        required: true,
      },
      {
        name: "totalPrice",
        label: $t("fuelingsTable.totalPrice"),
      },
      { name: "actions", label: "Akcje", align: "center" },
    ]);

    const showActionsColumn = ref(
      props.vehicle.canEdit || props.vehicle.canDelete
    );
    const visibleColumns = ref(["totalPrice"]);
    if (showActionsColumn.value) {
      visibleColumns.value.push("actions");
    }

    const totalPrice = (price, amount) => {
      let result = (price * amount).toFixed(2);
      if (result == 0) {
        result = null;
      }
      return result;
    };

    const getFuelingsQuery = `
      query getFuelings ($vehicleId: ID! $page: Int, $first: Int) {
        fuelings(
          vehicleId: $vehicleId
          page: $page
          first: $first
        ) {
          data {
            id
            amount
            mileage
            full
            fuelConsumption
            date
            price
          }
          paginatorInfo {
            total
            currentPage
          }
        }
      }
    `;

    const fuelings = ref([]);
    const loadingFuelings = ref(false);

    const paginationProps = ref({
      pagination: {
        page: 1,
        rowsPerPage: 5,
        rowsNumber: 0,
      },
    });

    async function getFuelings(pagination = paginationProps.value) {
      loadingFuelings.value = true;
      await apiClient
        .executeQuery({
          query: getFuelingsQuery,
          variables: {
            vehicleId: props.vehicle.id,
            page: pagination.pagination.page,
            first: 5,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          const fuelingsData = response.data.fuelings.data;
          paginationProps.value.pagination.rowsNumber =
            response.data.fuelings.paginatorInfo.total;
          paginationProps.value.pagination.page =
            response.data.fuelings.paginatorInfo.currentPage;
          fuelings.value = fuelingsData;
        });
      loadingFuelings.value = false;
    }

    getFuelings();

    return {
      columns,
      fuelings,
      loadingFuelings,
      color,
      paginationProps,
      getFuelings,
      visibleColumns,
      showAddFuelingDialog,
      showEditFuelingDialog,
      totalPrice,
      showPrice,
    };
  },
});
</script>

<style>
.fuelings-table {
  height: 406px;
  border: 1px solid rgba(0, 0, 0, 0.12);
}

.q-table__top {
  padding-right: 16px;
}
</style>
