<template>
  <q-dialog ref="dialogRef" no-backdrop-dismiss @hide="onDialogHide">
    <q-card class="q-dialog-plugin dialog-width">
      <q-card-section>
        <q-table
          square
          flat
          class="col-grow"
          :loading="loading"
          :columns="columns"
          :rows="fuelings"
          :visible-columns="visibleColumns"
          :no-data-label="$t('fuelingsTable.empty')"
          v-model:pagination="pagination"
          rows-per-page-options="5"
          icon-first-page="first_page"
          icon-last-page="last_page"
          @request="getFuelings"
        >
          <template v-slot:body-cell-full="props">
            <q-td :props="props">
              <q-icon :name="props.value ? 'mdi-check' : 'mdi-close'"></q-icon>
            </q-td>
          </template>
          <template v-slot:body-cell-pricePerLiter="props">
            <q-td :props="props">
              {{ pricePerLiter(props.row.price, props.row.amount) }}
            </q-td>
          </template>
        </q-table>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn flat :label="$t('routesTable.close')" @click="onDialogOK" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref } from "vue";
import { apiClient, handleErrors } from "src/boot/apiClient";
import { useDialogPluginComponent } from "quasar";
import { i18n } from "src/boot/i18n";

const $t = i18n.global.t;

const emits = defineEmits([...useDialogPluginComponent.emits]);
const { dialogRef, onDialogHide, onDialogOK } = useDialogPluginComponent();
const props = defineProps({
  routeId: String,
});

const getRouteQuery = `
      query getRoute ($routeId: ID!) {
        route(
          id: $routeId
        ) {
          fuelings {
            paginatorInfo {
              count
              currentPage
              firstItem
              hasMorePages
              lastItem
              lastPage
              perPage
              total
            }
            data {
              id
              amount
              mileage
              full
              fuelConsumption
              kilometerCost
              date
              price
            }
          }
        }
      }
    `;

const loading = ref(false);
const fuelings = ref([]);
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
    name: "pricePerLiter",
    label: $t("fuelingsTable.pricePerLiter"),
  },
]);

const visibleColumns = ref(["pricePerLiter"]);

const pricePerLiter = (price, amount) => {
  let result = (price / amount).toFixed(2);
  if (result == 0) {
    result = null;
  }
  return result;
};

const pagination = ref({
  page: 1,
  rowsPerPage: 15,
  rowsNumber: 0,
});

async function getRoute() {
  loading.value = true;
  await apiClient
    .executeQuery({
      query: getRouteQuery,
      variables: {
        routeId: props.routeId,
      },
    })
    .then((response) => {
      if (response.error) {
        handleErrors(response.error);
      }
      fuelings.value = response.data.route.fuelings.data;
      pagination.value.page =
        response.data.route.fuelings.paginatorInfo.currentPage;
      pagination.value.rowsPerPage =
        response.data.route.fuelings.paginatorInfo.perPage;
      pagination.value.rowsNumber =
        response.data.route.fuelings.paginatorInfo.count;
    });
  loading.value = false;
}

getRoute();
</script>

<style scoped>
.dialog-width {
  width: 700px;
  max-width: 80vw;
}
</style>
