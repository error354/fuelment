<template>
  <q-page>
    <div class="row justify-center q-pr-lg q-py-lg">
      <draggable
        tag="div"
        :list="vehicles"
        class="list-group full-width row justify-start q-col-gutter-lg"
        item-key="order"
        handle=".handle"
        @change="changeOrder"
      >
        <template #item="{ element }">
          <div class="col-12 col-md-6 col-lg-4 col-xl-3">
            <VehicleTable :vehicle="element" />
          </div>
        </template>
      </draggable>
    </div>
  </q-page>
</template>

<script>
import { defineComponent, ref } from "vue";
import { useProfileStore } from "../stores/profile.js";
import draggable from "vuedraggable";
import VehicleTable from "src/components/VehicleTable.vue";

export default defineComponent({
  name: "IndexPage",
  components: { draggable, VehicleTable },
  setup() {
    const profileStore = useProfileStore();
    const vehicles = ref(profileStore.getVehicles);
    const changeOrder = (changed) => {
      profileStore.updateVehicleOrder(
        changed.moved.element.id,
        changed.moved.newIndex
      );
    };
    return {
      vehicles,
      changeOrder,
    };
  },
});
</script>
