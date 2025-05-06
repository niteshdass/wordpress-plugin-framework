<template>
    <div>
        <Plus v-if="icon === 'ehxd-plus'" />
        <Cancel v-if="icon === 'ehxd-cancel'" />
        <Edit v-if="icon === 'ehxd-edit'" />
        <Delete v-if="icon === 'ehxd-delete'" />
        <Eye v-if="icon === 'ehxd-eye'" />
        <Money v-if="icon === 'ehxd-money'" />
        <Pending v-if="icon === 'ehxd-pending'" />
    </div>

  </template>
  

<script>
import Plus from "./Plus.vue";
import Cancel from "./Cancel.vue";
import Edit from "./Edit.vue";
import Delete from "./Delete.vue";
import Eye from "./Eye.vue";
import Money from "./Money.vue";
import Pending from "./PendingIcon.vue";

export default {
    components: {
        Plus,
        Cancel,
        Edit,
        Delete,
        Eye,
        Money,
        Pending
    },
    props: {
        icon: {
            type: String,
        }
    }
}
</script>