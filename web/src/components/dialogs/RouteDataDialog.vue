<template>
  <q-dialog ref="dialogRef">
    <q-card class="q-dialog-plugin">
      <q-card-section>
        <div class="text-h6">Szczegóły trasy</div>
      </q-card-section>
      <q-card-section>
        <data-list>
          <data-list-element
            :name="$t('routesTable.avgFuelConsumption')"
            :value="route.avgFuelConsumption"
            :loading="loadingRoute"
            light
          />
          <data-list-element
            :name="$t('routesTable.distance')"
            :value="route.distance"
            :loading="loadingRoute"
            light
          />
          <data-list-element
            :name="$t('routesTable.kilometerCost')"
            :value="route.kilometerCost"
            :loading="loadingRoute"
            light
          />
          <data-list-element
            :name="$t('routesTable.totalCost')"
            :value="route.totalCost"
            :loading="loadingRoute"
            light
          />
          <data-list-element
            :name="$t('routesTable.totalAmount')"
            :value="route.totalAmount"
            :loading="loadingRoute"
            light
          />
        </data-list>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn color="primary" flat label="Zamknij" @click="onCancelClick" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useDialogPluginComponent } from "quasar";
import { apiClient, handleErrors } from "src/boot/apiClient";
import DataList from "src/components/DataList.vue";
import DataListElement from "src/components/DataListElement.vue";

export default defineComponent({
  name: "RouteDataDialog",
  props: {
    routeId: Number,
  },
  components: {
    DataList,
    DataListElement,
  },
  emits: [...useDialogPluginComponent.emits],

  setup(props) {
    const { dialogRef, onDialogCancel } = useDialogPluginComponent();

    const loadingRoute = ref(false);
    const route = ref({});
    const getRouteQuery = `
      query getRoute ($routeId: ID!) {
        route(
          id: $routeId
        ) {
          avgFuelConsumption
          distance
          kilometerCost
          totalCost
          totalAmount
        }
      }
    `;

    async function getRoute() {
      loadingRoute.value = true;
      await apiClient
        .executeQuery({
          query: getRouteQuery,
          variables: {
            routeId: props.routeId,
          },
          tags: [`route`],
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          route.value = response.data.route;
        });
      loadingRoute.value = false;
    }

    getRoute();

    return {
      dialogRef,
      onCancelClick: onDialogCancel,
      props,
      loadingRoute,
      route,
    };
  },
});
</script>
