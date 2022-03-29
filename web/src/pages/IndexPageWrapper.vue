<template>
  <q-page
    class="fit column justify-center items-center content-center"
    v-if="!skeletonsNumber"
  >
    <q-icon name="mdi-gauge-empty" size="120px" />
    <span class="text-h5">{{ $t("noVehicles") }}</span>
  </q-page>
  <Suspense v-else>
    <!-- component with nested async dependencies -->
    <IndexPage />
    <!-- loading state via #fallback slot -->
    <template #fallback>
      <q-page>
        <div class="row justify-center q-pr-lg q-py-lg">
          <div class="full-width row justify-start q-col-gutter-lg">
            <VehicleTableLoadingSkeleton
              v-for="n in skeletonsNumber"
              :key="n"
            />
          </div>
        </div>
      </q-page>
    </template>
  </Suspense>
</template>

<script>
import { ref } from "vue";
import { useProfileStore } from "../stores/profile.js";
import { storeToRefs } from "pinia";
import IndexPage from "./IndexPage.vue";
import VehicleTableLoadingSkeleton from "src/components/VehicleTableLoadingSkeleton.vue";

export default {
  components: { IndexPage, VehicleTableLoadingSkeleton },
  setup() {
    const profileStore = useProfileStore();
    const { vehiclesCount } = storeToRefs(profileStore);
    const skeletonsNumber = ref(vehiclesCount.value);

    if (vehiclesCount.value > 6) {
      skeletonsNumber.value = 6;
    }

    return {
      skeletonsNumber,
    };
  },
};
</script>
