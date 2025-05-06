<template>
    <div class="ehxd_wrapper">

        <AppTable :tableData="books"  v-loading="loading">
            <template #header>
                <h1 class="table-title">All Menu Item</h1>
                <el-button  size="large" type="primary" icon="Plus" class="ltm_button">
                    Add New Menu Item
                </el-button>
            </template>

             <template #filter>
                <el-input  class="ehxd-search-input ehxd_input" v-model="search" style="width: 240px" size="large"
                    placeholder="Please Input" prefix-icon="Search" />
            </template>
           
            <template #columns>
                <el-table-column prop="id" label="ID" width="60" />
                <el-table-column label="Image" width="auto">
                    <template #default="{ row }">
                        <img v-if="row.images?.url" :src="row.images?.url" alt="image" style="width: 60px; height: 60px; object-fit: cover;">
                        <span v-else>No Image</span>
                    </template>
                </el-table-column>
                <el-table-column prop="branch_id" label="Branch Id" width="auto" />
                <el-table-column prop="category_id"  label="Category" width="auto" />
                <el-table-column prop="name" label="Name" width="auto" />
                <el-table-column prop="base_price" label="Price" width="auto" />
                <el-table-column prop="addons" label="Addons" width="auto" />
                <el-table-column prop="portion_sizes" label="Portion Sizes" width="auto" />
                <el-table-column prop="availability" label="Availability" width="auto" />
                <el-table-column prop="added_date" label="Add Date" width="auto" >
                    <template #default="{ row }">
                        {{ formatAddedDate(row.added_date) }}
                    </template>
                </el-table-column>
                <el-table-column label="Operations" width="120">
                    <template #default="{ row }">
                        <el-tooltip class="box-item" effect="dark" content="Click to view books" placement="top-start">
                            <el-button  class="ehxd_box_icon"  link  size="small">
                                <Icon icon="ehxd-edit" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to delete books" placement="top-start">
                            <el-button   class="ehxd_box_icon" link  size="small">
                                <Icon icon="ehxd-delete" />
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </template>

            <template #footer>
                <el-pagination
                    v-model:current-page="currentPage"
                    v-model:page-size="pageSize"
                    :page-sizes="[10, 20, 30, 40]"
                    large
                    :disabled="total_book <= pageSize"
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="+total_book"
                />
            </template>

        </AppTable>

    </div>
</template>

<script>
import AppTable from "../../components/AppTable.vue";
import Icon from "../../components/Icons/AppIcon.vue";
export default {
    components: {
        AppTable,
        Icon,
    },
    data() {
        return {
            search: '',
            books: [],
            book: {},
            total_book: 0,
            loading: false,
            add_books_modal: false,
            currentPage: 1,
            pageSize: 10,
            active_id : null
        }
    },

 
}
</script>