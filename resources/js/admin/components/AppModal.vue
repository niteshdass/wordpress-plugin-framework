<template>
    <el-dialog v-model="dialogVisible" class="ehxd-app-modal" :visible.sync="dialogVisible" :title="title"
        width="500" :before-close="handleClose">
        <slot name="body"></slot>
        <slot name="footer"></slot>
    </el-dialog>
</template>


<script>
export default {
    data() {
        return {
            dialogVisible: false,
        };
    },
    props: {
        title: {
            type: String,
            default: "Dialog",
        },
        confirmButtonText: {
            type: String,
            default: "Confirm",
        },
        cancelButtonText: {
            type: String,
            default: "Cancel",
        },
        showFooter: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        handleClose(done) {
           this.dialogVisible = false;
        },
        openModel() {
            this.dialogVisible = true;
        },
    },
};
</script>