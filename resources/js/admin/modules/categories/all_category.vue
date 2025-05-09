<template>
    <div class="ehxd_wrapper">

        <AppModal :title="'Add New Category'" :width="700" :showFooter="false" ref="add_category_modal">
            <template #body>
                <AddCategory />
            </template>
        </AppModal>

        <AppTable :tableData="books" v-loading="loading">
            <template #header>
                <h1 class="table-title">All Category</h1>
                <el-button @click="openCategoryAddModal()" size="large" type="primary" icon="Plus" class="ltm_button">
                    Add New Category nnn
                </el-button>
            </template>

            <template #filter>
                <el-input class="ehxd-search-input ehxd_input" v-model="search" style="width: 240px" size="large"
                    placeholder="Please Input" prefix-icon="Search" />
                <GoogleMapAddress />
            </template>

            <template #columns>
                <el-table-column prop="id" label="ID" width="60" />
                <el-table-column prop="branch_id" label="Branch Id" width="auto" />
                <el-table-column prop="name" label="Name" width="auto" />
                <el-table-column prop="added_date" label="Add Date" width="auto">
                    <template #default="{ row }">
                        <!-- {{ formatAddedDate(row.added_date) }} -->
                    </template>
                </el-table-column>
                <el-table-column label="Operations" width="120">
                    <template #default="{ row }">
                        <el-tooltip class="box-item" effect="dark" content="Click to view books" placement="top-start">
                            <el-button class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-edit" />
                            </el-button>
                        </el-tooltip>
                        <el-tooltip class="box-item" effect="dark" content="Click to delete books"
                            placement="top-start">
                            <el-button class="ehxd_box_icon" link size="small">
                                <Icon icon="ehxd-delete" />
                            </el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>
            </template>

            <template #footer>
                <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize"
                    :page-sizes="[10, 20, 30, 40]" large :disabled="total_book <= pageSize" background
                    layout="total, sizes, prev, pager, next" :total="+total_book" />
            </template>

        </AppTable>

    </div>
</template>




<script>
import GoogleMapAddress from "../../components/GoogleMapAddress.vue";
import AppTable from "../../components/AppTable.vue";
import Icon from "../../components/Icons/AppIcon.vue";
import AppModal from "../../components/AppModal.vue";
import AddCategory from "./add_category.vue";
import axios from "axios";
export default {
    components: {
        AppTable,
        Icon,
        AppModal,
        AddCategory,
        GoogleMapAddress
    },
    data() {
        return {
            search: '',
            books: [],
            book: {},
            total_book: 0,
            loading: false,
            currentPage: 1,
            pageSize: 10,
            active_id: null,
            add_category_modal: false,
            rest_api: window.EhxDirectoristData.rest_api,
        }
    },

    methods: {
        openCategoryAddModal() {
            if (this.$refs.add_category_modal) {
                console.log('hello', this.$refs.add_category_modal.openModel());
                this.$refs.add_category_modal.openModel();
            } else {
                console.log("Modal ref not found! Ensure AppModal is rendered.");
            }
        },

        async fetchCouriers() {
            try {
                const response = await axios.get(`${this.rest_api}/get-categories`);
                console.log('Category:', response.data);
            } catch (error) {
                console.error('Error fetching category:', error);
            }
        },
    },
    mounted() {
        this.fetchCouriers();
    },


}
</script>