<template>
    <div class="ehxd_form_wrapper">

        <div class="input-wrapper">
            <p class="form-label" for="name">Category Name *</p>
            <el-input class="ehxd_input" v-model="categories.name" style="width: 100%"
                placeholder="Please Input Category Name" size="large" />
            <p class="error-message" style="margin: 0px 0px 10px 0px;">{{ name_error }}</p>
        </div>

        <div class="input-wrapper">
            <p class="form-label" for="name">Brunch Name *</p>

            <el-select class="ehxd_input" v-model="categories.branch_id" placeholder="Brunch Name" size="large"
                style="width: 100%">
                <el-option value="sylhet" />
                <el-option value="dhaka" />
            </el-select>
            <p class="error-message" style="margin: 0px 0px 10px 0px;">{{ branch_id_error }}</p>
        </div>

        <div class="input-wrapper">
            <p class="form-label" for="name">Description</p>
            <el-input class="ehxd_input" v-model="categories.description" style="width: 100%"
                placeholder="Please Input Description" size="large" type="textarea" />
        </div><br>

        <div class="input-wrapper" @click="saveCategory()">
            <el-button size="large" type="primary">Save Category</el-button>
        </div>

    </div>
</template>

<script>
import axios from "axios"; // Import Axios

export default {
    data() {
        return {
            categories: {
                name: "",
                branch_id: "",
                description: "",
            },
            name_error: "",
            branch_id_error: "",
            rest_api: window.EhxDirectoristData.rest_api,
        };
    },

    methods: {
        async saveCategory() {
            this.name_error = "";
            this.branch_id_error = "";
            if (!this.categories.name) {
                this.name_error = "Category name is required";
                return;
            }
            if (!this.categories.branch_id) {
                this.branch_id_error = "Brunch name is required";
                return;
            }
            try {
                const response = await axios.post(`${this.rest_api}/postCategory`, this.categories);
                console.log('Category:', response.data);
            } catch (error) {
                console.error('Error fetching category:', error);
            }
        },


    }
};
</script>