<template>
  <q-page>
    <div class="row justify-center q-pr-lg q-py-lg">
      <draggable
        :list="vehicles"
        class="list-group full-width row justify-start q-col-gutter-lg"
        item-key="order"
        handle=".handle"
        tag="transition-group"
        :component-data="{
          tag: 'div',
          type: 'transition-group',
          name: !drag ? 'flip-list' : null,
        }"
        v-bind="dragOptions"
        @start="drag = true"
        @end="drag = false"
        @change="changeOrder"
      >
        <template #item="{ element }">
          <div class="col-12 col-lg-6 col-xl-4">
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
import draggable from "zhyswan-vuedraggable";
import VehicleTable from "src/components/VehicleTable.vue";

export default defineComponent({
  name: "IndexPage",
  components: { draggable, VehicleTable },
  async setup() {
    const profileStore = useProfileStore();
    await profileStore.fetchVehicles();
    const vehicles = ref(profileStore.getVehicles);

    const drag = ref(false);
    const dragOptions = ref({
      animation: 200,
      ghostClass: "ghost",
    });
    const changeOrder = (changed) => {
      profileStore.updateVehicleOrder(
        changed.moved.element.id,
        changed.moved.newIndex
      );
    };

    return {
      vehicles,
      changeOrder,
      drag,
      dragOptions,
    };
  },
});
</script>

<style>
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
}
</style>
