<template>
  <q-icon
    name="mdi-circle-medium"
    class="cursor-default"
    size="md"
    :color="color"
  >
    <q-tooltip self="center middle">
      {{ $t(`fuelingsTable.fuel.${fuel}`) }}
    </q-tooltip>
  </q-icon>
</template>

<script>
import { defineComponent, ref } from "vue";

export default defineComponent({
  name: "FuelTypeDot",
  props: {
    fuel: String,
  },
  setup(props) {
    let color = ref("blue-grey");
    switch (props.fuel) {
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

    return { color };
  },
});
</script>
