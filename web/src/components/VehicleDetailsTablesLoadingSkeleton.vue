<template>
  <div class="col-12 col-lg-6 col-xl-4">
    <div class="row q-mb-sm q-mt-md">
      <q-skeleton
        animation="blink"
        type="circle"
        size="12px"
        class="q-mr-md q-ml-xs"
      />
      <q-skeleton square animation="blink" type="rect" style="width: 150px" />
      <q-space />
      <q-skeleton square animation="blink" type="rect" style="width: 150px" />
      <q-skeleton square animation="blink" type="rect" style="width: 150px" />
    </div>
    <q-table square flat class="col-grow table">
      <template #header>
        <q-tr>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
          <q-th>
            <q-skeleton
              square
              animation="blink"
              type="text"
              style="width: 50px"
            />
          </q-th>
        </q-tr>
      </template>
      <template #body>
        <q-tr>
          <q-td>
            <q-skeleton animation="blink" type="text" width="85px" />
          </q-td>
        </q-tr>
      </template>
      <template #no-data>
        <q-skeleton square animation="blink" type="rect" style="width: 100px" />
      </template>
    </q-table>
  </div>
</template>

<style scoped>
.table {
  height: calc(100vh - 150px);
  min-height: 600px;
}
</style>
