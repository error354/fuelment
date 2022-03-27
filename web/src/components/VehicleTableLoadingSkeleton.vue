<template>
  <div class="col-12 col-md-6 col-lg-4 col-xl-3">
    <q-table square flat class="col-grow fuelings-table">
      <template #top>
        <div class="relative-position row items-center full-width q-pt-sm">
          <q-skeleton
            animation="blink"
            type="circle"
            size="12px"
            class="q-mr-md q-ml-xs"
          />
          <q-skeleton
            square
            animation="blink"
            type="rect"
            style="width: 150px"
          />
          <q-space />
          <q-skeleton
            square
            animation="blink"
            type="rect"
            style="width: 150px"
            class="q-mr-sm"
          />
        </div>
        <div class="relative-position row items-center full-width q-pt-lg">
          <q-skeleton
            square
            animation="blink"
            type="text"
            style="width: 50px"
          />
          <q-space />
          <q-skeleton
            square
            animation="blink"
            type="text"
            style="width: 50px"
          />
          <q-space />
          <q-skeleton
            square
            animation="blink"
            type="text"
            style="width: 50px"
          />
          <q-space />
          <q-skeleton
            square
            animation="blink"
            type="text"
            style="width: 50px"
          />
          <q-space />
          <q-skeleton
            square
            animation="blink"
            type="text"
            style="width: 50px"
          />
          <q-space />
          <q-skeleton
            square
            animation="blink"
            type="text"
            style="width: 50px"
          />
        </div>
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
