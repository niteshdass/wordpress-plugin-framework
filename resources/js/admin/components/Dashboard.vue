<template>
  <el-table :data="tableData" style="width: 100%">
    <el-table-column prop="date" label="Date" width="180" />
    <el-table-column prop="name" label="Name" width="180" />
    <el-table-column prop="address" label="Address" />
  </el-table>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      tableData: [
        {
          date: '2016-05-03',
          name: 'Tom',
          address: 'No. 189, Grove St, Los Angeles',
        },
        {
          date: '2016-05-02',
          name: 'Tom',
          address: 'No. 189, Grove St, Los Angeles',
        },
        {
          date: '2016-05-04',
          name: 'Tom',
          address: 'No. 189, Grove St, Los Angeles',
        },
        {
          date: '2016-05-01',
          name: 'Tom',
          address: 'No. 189, Grove St, Los Angeles',
        },
      ],
      rest_api: window.EhxDirectoristData.rest_api,
    }
  },
  methods: {
      async fetchCouriers() {
          try {
              const response = await axios.get(`${this.rest_api}/test`);
              console.log('Couriers:', response.data);
          } catch (error) {
              console.error('Error fetching couriers:', error);
          }
      },
  },
  mounted() {
    this.fetchCouriers();
  }
}
</script>