<x-site.layout>
    <div class="container mx-auto p-8 max-w-4xl">
        <!-- Page Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-amber-800 mb-2">☕ Coffee Shop Reservations</h1>
            <p class="text-gray-600">Book your table easily and track your reservations in style</p>
        </div>

        <!-- Reservation Form -->
        <div class="mb-12 bg-gradient-to-r from-amber-100 to-amber-50 p-8 rounded-xl shadow-lg border border-amber-200">
            <h2 class="text-2xl font-bold mb-6 text-amber-900">Make a Reservation</h2>
            <form id="reservationForm" class="grid gap-5">
                <input type="text" id="name" placeholder="Your Name" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:outline-none" required>
                <input type="text" id="mobile" placeholder="Mobile Number" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:outline-none" required>
                <div class="grid grid-cols-2 gap-5">
                    <input type="date" id="date" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:outline-none" required>
                    <input type="time" id="time" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-400 focus:outline-none" required>
                </div>
                <button type="submit" 
                        class="bg-amber-700 hover:bg-amber-800 transition text-white px-6 py-3 rounded-lg font-semibold shadow-md">
                    Book Now
                </button>
            </form>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button onclick="loadReservations()" class="filterBtn">All</button>
            <button onclick="loadReservations('pending')" class="filterBtn">Pending</button>
            <button onclick="loadReservations('approved')" class="filterBtn">Approved</button>
            <button onclick="loadReservations('not approved')" class="filterBtn">Not Approved</button>
            <button onclick="loadReservations('canceled')" class="filterBtn">Canceled</button>
            <button onclick="loadReservations('expired')" class="filterBtn">Expired</button>
        </div>

        <!-- Reservations List -->
        <div id="reservationsList" class="grid md:grid-cols-2 gap-6"></div>
    </div>

    <!-- Custom Styles for filter buttons -->
    <style>
        .filterBtn {
            @apply px-4 py-2 rounded-full font-medium border border-gray-300 bg-white 
                   text-gray-700 shadow-sm hover:bg-amber-100 transition;
        }
        .status-badge {
            @apply px-3 py-1 rounded-full text-sm font-semibold;
        }
        .status-pending { @apply bg-yellow-100 text-yellow-700; }
        .status-approved { @apply bg-green-100 text-green-700; }
        .status-canceled { @apply bg-red-100 text-red-700; }
        .status-not-approved { @apply bg-gray-200 text-gray-700; }
        .status-expired { @apply bg-gray-300 text-gray-800; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const API_URL = "/api/v1/reservations";

        function statusBadge(status) {
            const cls = {
                "pending": "status-pending",
                "approved": "status-approved",
                "canceled": "status-canceled",
                "not approved": "status-not-approved",
                "expired": "status-expired"
            }[status] || "bg-gray-100 text-gray-700";
            return `<span class="status-badge ${cls}">${status}</span>`;
        }

        // Load reservations
        function loadReservations(status = null) {
            let url = API_URL;
            if (status) url += "?status=" + status;

            axios.get(url).then(res => {
                const list = document.getElementById("reservationsList");
                list.innerHTML = "";

                res.data.forEach(r => {
                    const card = document.createElement("div");
                    card.className = "p-6 bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition";

                    card.innerHTML = `
                        <h3 class="text-lg font-bold text-gray-800 mb-1">${r.name}</h3>
                        <p class="text-sm text-gray-500 mb-3">${r.mobile}</p>
                        <p class="text-gray-700"><strong>Date:</strong> ${r.date}</p>
                        <p class="text-gray-700 mb-3"><strong>Time:</strong> ${r.time}</p>
                        <div class="mb-3">${statusBadge(r.status)}</div>
                        ${r.status === 'pending' ? `
                            <button onclick="cancelReservation('${r._id}')" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm shadow">
                                Cancel
                            </button>` : ''}
                    `;

                    list.appendChild(card);
                });
            });
        }

        // Submit reservation
        document.getElementById("reservationForm").addEventListener("submit", e => {
            e.preventDefault();
            axios.post(API_URL, {
                name: document.getElementById("name").value,
                mobile: document.getElementById("mobile").value,
                date: document.getElementById("date").value,
                time: document.getElementById("time").value,
            }).then(() => {
                e.target.reset();
                loadReservations();
            });
        });

        // Cancel reservation
        function cancelReservation(id) {
            axios.put(`${API_URL}/${id}`, { status: "canceled" }).then(() => loadReservations());
        }

        // Initial load
        loadReservations();
    </script>
</x-site.layout>
