<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from '../components/Sidebar.vue';
import Navbar from '../components/Navbar.vue';
import DepositWithdraw from '../components/DepositWithdraw.vue';
import TransactionHistory from '../components/TransactionHistory.vue';

const router = useRouter();
const userData = ref({});
const currentTab = ref('deposit-withdraw');

onMounted(() => {
  const storedUser = localStorage.getItem('user_data');
  const token = localStorage.getItem('user_token');

  if (!storedUser || !token) {
    router.push('/');
    return;
  }

  userData.value = JSON.parse(storedUser);
});

const handleLogout = () => {
  localStorage.removeItem('user_token');
  localStorage.removeItem('user_data');
  router.push('/');
};

const updateBalance = (newBalance) => {
    userData.value.balance = newBalance;
    localStorage.setItem('user_data', JSON.stringify(userData.value));
};
</script>

<template>

  <div class="d-flex flex-column vh-100 bg-dark text-white overflow-hidden">
    <Navbar :userData="userData" @logout="handleLogout" />
    <div class="d-flex flex-grow-1 overflow-hidden">
      <div class="d-none d-lg-block h-100">
        <Sidebar :currentTab="currentTab" @update:currentTab="currentTab = $event" />
      </div>
      <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom border-secondary">
          <h5 class="offcanvas-title fw-bold text-white">Menu</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
          <Sidebar :currentTab="currentTab" @update:currentTab="currentTab = $event" />
        </div>
      </div>
      <div class="flex-grow-1 overflow-auto bg-black bg-opacity-25">
        <div class="container-fluid p-3 p-md-5">
          <div style="max-width: 900px; margin: 0 auto;">
            <div v-if="currentTab === 'deposit-withdraw'">
                <DepositWithdraw :userData="userData" @balance-updated="updateBalance" />
            </div>
            <div v-else>
                <TransactionHistory @balance-updated="updateBalance" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped>
</style>