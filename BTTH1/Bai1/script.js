// 1. DỮ LIỆU KHỞI TẠO
let flowers = [
  {
    id: 1,
    name: "Hoa Dạ Yến Thảo",
    description:
      "Dạ yến thảo là lựa chọn thích hợp cho những ai yêu thích trồng hoa làm đẹp nhà ở. Hoa có thể nở rực quanh năm.",
    image: "images/hoaDaYenThao.jpg",
  },
  {
    id: 2,
    name: "Hoa Dừa Cạn",
    description:
      "Dừa cạn rủ rất thích hợp để trồng trong các chậu treo. Loài hoa này chịu nắng nóng rất tốt.",
    image: "images/hoaDuaCan.jpg",
  },
  {
    id: 3,
    name: "Hoa Triệu Chuông",
    description:
      "Triệu chuông có hình dáng nhỏ xinh như những chiếc chuông, số lượng hoa rất nhiều và rực rỡ.",
    image: "images/hoaTrieuChuong.jpg",
  },
  {
    id: 4,
    name: "Hoa Thanh Tú",
    description:
      "Hoa thanh tú sở hữu màu thiên thanh dịu dàng. Hoa dễ trồng, rất ưa nắng.",
    image: "images/hoaThanhTu.jpg",
  },
];

let currentMode = "guest";

// 2. CÁC HÀM RENDER

// Render Guest View
function renderGuestView() {
  const container = document.getElementById("guest-flower-list");
  container.innerHTML = "";

  flowers.forEach((flower) => {
    const card = `
            <div class="flower-card bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-2xl transition-shadow duration-300">
                <div class="h-64 overflow-hidden relative">
                    <img src="${flower.image}" alt="${flower.name}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/600x400?text=Chưa+có+ảnh'">
                    <div class="absolute bottom-0 left-0 bg-gradient-to-t from-black/70 to-transparent w-full p-4">
                        <h3 class="text-white text-xl font-bold">${flower.name}</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 leading-relaxed">${flower.description}</p>
                </div>
            </div>
        `;
    container.innerHTML += card;
  });
}

// Render Admin View
function renderAdminView() {
  const tbody = document.getElementById("admin-flower-table");
  tbody.innerHTML = "";

  flowers.forEach((flower) => {
    const row = `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-6 text-left">
                    <div class="w-16 h-16 rounded overflow-hidden border">
                        <img src="${flower.image}" alt="${flower.name}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                    </div>
                </td>
                <td class="py-3 px-6 text-left font-medium whitespace-nowrap">${flower.name}</td>
                <td class="py-3 px-6 text-left max-w-xs truncate" title="${flower.description}">${flower.description}</td>
                <td class="py-3 px-6 text-center">
                    <div class="flex item-center justify-center gap-3">
                        <button onclick="openModal(${flower.id})" class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 hover:bg-yellow-200 flex items-center justify-center" title="Sửa">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>
                        <button onclick="deleteFlower(${flower.id})" class="w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center" title="Xóa">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    tbody.innerHTML += row;
  });
}

// 3. LOGIC CHUYỂN ĐỔI CHẾ ĐỘ
function switchMode(mode) {
  currentMode = mode;
  const guestView = document.getElementById("guest-view");
  const adminView = document.getElementById("admin-view");
  const btnGuest = document.getElementById("btn-guest");
  const btnAdmin = document.getElementById("btn-admin");

  if (mode === "guest") {
    guestView.classList.remove("hidden");
    adminView.classList.add("hidden");
    btnGuest.classList.add("bg-white", "text-teal-700", "shadow-sm");
    btnGuest.classList.remove("text-teal-100");
    btnAdmin.classList.remove("bg-white", "text-teal-700", "shadow-sm");
    btnAdmin.classList.add("text-teal-100");
    renderGuestView();
  } else {
    guestView.classList.add("hidden");
    adminView.classList.remove("hidden");
    btnAdmin.classList.add("bg-white", "text-teal-700", "shadow-sm");
    btnAdmin.classList.remove("text-teal-100");
    btnGuest.classList.remove("bg-white", "text-teal-700", "shadow-sm");
    btnGuest.classList.add("text-teal-100");
    renderAdminView();
  }
}

// 4. CRUD OPERATIONS

function openModal(id = null) {
  const modal = document.getElementById("flower-modal");
  const form = document.getElementById("flower-form");
  const title = document.getElementById("modal-title");

  form.reset();

  if (id) {
    const flower = flowers.find((f) => f.id === id);
    if (flower) {
      document.getElementById("flower-id").value = flower.id;
      document.getElementById("name").value = flower.name;
      document.getElementById("image").value = flower.image;
      document.getElementById("description").value = flower.description;
      title.innerText = "Cập Nhật Thông Tin";
    }
  } else {
    document.getElementById("flower-id").value = "";
    title.innerText = "Thêm Loài Hoa Mới";
  }
  modal.classList.remove("hidden");
}

function closeModal() {
  document.getElementById("flower-modal").classList.add("hidden");
}

function handleFormSubmit(event) {
  event.preventDefault();

  const id = document.getElementById("flower-id").value;
  const name = document.getElementById("name").value;
  const image = document.getElementById("image").value;
  const description = document.getElementById("description").value;

  if (id) {
    // UPDATE
    const index = flowers.findIndex((f) => f.id == id);
    if (index !== -1) {
      flowers[index] = { ...flowers[index], name, image, description };
    }
  } else {
    // CREATE
    const newId =
      flowers.length > 0 ? Math.max(...flowers.map((f) => f.id)) + 1 : 1;
    flowers.push({
      id: newId,
      name,
      image,
      description,
    });
  }

  closeModal();
  if (currentMode === "admin") renderAdminView();
  else renderGuestView();
}

function deleteFlower(id) {
  if (confirm("Bạn có chắc chắn muốn xóa loài hoa này không?")) {
    flowers = flowers.filter((f) => f.id !== id);
    renderAdminView(); // Luôn cập nhật admin view khi xóa
  }
}

// KHỞI CHẠY
document.addEventListener("DOMContentLoaded", () => {
  switchMode("guest");
});
