
/* ========================================
   MOBILE SIDEBAR
======================================== */

const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");

menuBtn.addEventListener("click", function () {

    sidebar.classList.toggle("show");

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

