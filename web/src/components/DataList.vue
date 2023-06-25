<template>
  <div class="full-width row q-mb-md">
    <div class="col-12">
      <h6 class="q-my-xs">
        <slot name="title">{{ title }}</slot>
      </h6>
    </div>
    <slot> </slot>
  </div>
</template>

<script>
export default {
  name: "DataList",
  props: {
    title: String,
  },
};
</script>
