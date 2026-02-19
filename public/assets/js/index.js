const menyknapp = document.getElementById("menyknapp");
const toppmenyLankar = document.getElementById("toppmenyLankar");

if (menyknapp && toppmenyLankar) {
  menyknapp.addEventListener("click", () => {
    const arOppnad = toppmenyLankar.classList.toggle("ar-oppnad");
    menyknapp.setAttribute("aria-expanded", String(arOppnad));
  });

  // Stäng menyn när man klickar på en länk (mobil)
  toppmenyLankar.addEventListener("click", (e) => {
    const mal = e.target;
    if (mal && mal.tagName === "A" && toppmenyLankar.classList.contains("ar-oppnad")) {
      toppmenyLankar.classList.remove("ar-oppnad");
      menyknapp.setAttribute("aria-expanded", "false");
    }
  });
}
