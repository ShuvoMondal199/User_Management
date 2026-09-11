
/* ========================================
   MOBILE SIDEBAR
======================================== */

const menuBtn = document.getElementById("menuBtn");
const sidebar = document.querySelector(".sidebar");
const mainContent = document.querySelector(".main-content");

menuBtn.addEventListener("click", function () {
    sidebar.classList.toggle("show");
    mainContent.classList.toggle("sidebar-open");
});


/* ========================================
   USER TABLE SEARCH
======================================== */

const userSearch = document.getElementById("userSearch");
const tableRows = document.querySelectorAll(
    "#usersTable tbody tr"
);

userSearch.addEventListener("keyup", function () {

    const searchValue =
        this.value.toLowerCase().trim();


    tableRows.forEach(function (row) {

        const rowText =
            row.textContent.toLowerCase();


        if (rowText.includes(searchValue)) {

            row.style.display = "";

        } else {

            row.style.display = "none";

        }

    });

});



/* ========================================
   GLOBAL SEARCH
======================================== */

const globalSearch =
    document.getElementById("globalSearch");

globalSearch.addEventListener("keyup", function () {

    const searchValue =
        this.value.toLowerCase().trim();


    tableRows.forEach(function (row) {

        const rowText =
            row.textContent.toLowerCase();


        if (rowText.includes(searchValue)) {

            row.style.display = "";

        } else {

            row.style.display = "none";

        }

    });

});

