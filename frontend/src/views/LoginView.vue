<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const email = ref("");
const password = ref("");

const showPassword = ref(false);

const errors = ref({
  email: "",
  password: "",
});

const isLoading = ref(false);

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const validateForm = () => {
  let isValid = true;
  errors.value = { email: "", password: "" };

  if (!email.value) {
    errors.value.email = "กรุณากรอกอีเมล";
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = "รูปแบบอีเมลไม่ถูกต้อง";
    isValid = false;
  }

  if (!password.value) {
    errors.value.password = "กรุณากรอกรหัสผ่าน";
    isValid = false;
  } else if (password.value.length < 6) {
    errors.value.password = "รหัสผ่านต้องมีความยาวไม่ต่ำกว่า 6 ตัวอักษร";
    isValid = false;
  }

  return isValid;
};

const handleLogin = async () => {
  if (!validateForm()) return;

  isLoading.value = true;

  try {
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value
      })
    });

    const data = await response.json();

    if (response.ok) {
      localStorage.setItem("user_token", data.token);
      localStorage.setItem("user_data", JSON.stringify(data.user));

      router.push('/banking');
    } else {
      alert(data.message || "การเข้าสู่ระบบล้มเหลว");
    }
  
  } catch (error) {
    console.error('Error:', error);
    alert("ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้");
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="d-flex justify-content-center align-items-center vh-100 bg-dark p-4">

    <div class="card bg-dark bg-gradient text-white shadow" style="max-width: 400px; width: 100%">
     
      <img src="../assets/images/banner.png" style="max-width: 100%; height: auto;" class="card-img-top" alt="Banking Web App banner">
     
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <h2 class="text-white fw-bold fs-3">เข้าสู่ระบบ</h2>
          <p class="text-white-50 small">กรอกรหัสผ่านและอีเมลของคุณเพื่อเข้าสู่ระบบ</p>
        </div>

        <form @submit.prevent="handleLogin">         
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group has-validation">
              <span class="input-group-text bg-secondary border-secondary text-white">
                <i class="bi bi-envelope-fill"></i>
              </span>
              <input type="email" id="email" class="form-control bg-dark text-white border-secondary" :class="{ 'is-invalid': errors.email }" v-model="email" placeholder="example@domain.com" />
              <div class="invalid-feedback">
                {{ errors.email }}
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group has-validation">
              <span class="input-group-text bg-secondary border-secondary text-white">
                <i class="bi bi-key-fill"></i>
              </span>
              <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control bg-dark text-white border-secondary" :class="{ 'is-invalid': errors.password }" v-model="password" placeholder="password" />
              <button class="btn btn-outline-secondary text-white" type="button" @click="togglePasswordVisibility" tabindex="-1">
                <i :class="showPassword ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill'"></i>
              </button>
              <div class="invalid-feedback">
                {{ errors.password }}
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 mt-3 fw-bold shadow-sm" :disabled="isLoading">
            <span v-if="isLoading">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            </span>
            <span v-else>เข้าสู่ระบบ</span>
          </button>
        </form>

        <p class="text-center text-white-50 mt-4 small">
          หากไม่มีบัญชีสามารถสมัครสมาชิกได้ <a href="#" class="text-primary-500 fw-bold">ที่นี่</a>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
::placeholder {
  color: #adb5bd !important;
  opacity: 0.5;
}
</style>