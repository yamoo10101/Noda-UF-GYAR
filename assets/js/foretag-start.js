// assets/js/foretag-start.js

// Flikar
const foretagFlikLankar = document.querySelectorAll("[data-flik]");
const foretagFlikar = {
  annonser: document.getElementById("flik-annonser"),
  skapa: document.getElementById("flik-skapa"),
  swipe: document.getElementById("flik-swipe"),
  matchningar: document.getElementById("flik-matchningar"),
  profil: document.getElementById("flik-profil"),
};

function visaForetagFlik(namn) {
  Object.values(foretagFlikar).forEach((el) => el && el.classList.remove("flik--aktiv"));
  if (foretagFlikar[namn]) foretagFlikar[namn].classList.add("flik--aktiv");
}

foretagFlikLankar.forEach((l) => {
  l.addEventListener("click", (e) => {
    e.preventDefault();
    visaForetagFlik(l.dataset.flik);
  });
});

// Fejk-swipe: byter profilkort
const profiler = [
  {
    namn: "Alex Andersson",
    stad: "Alingsås",
    bio: "Jag är social, gillar tempo och söker jobb inom service. Jag kan jobba kvällar och helger.",
    taggar: ["Service", "Team", "Helg", "Kundkontakt"],
  },
  {
    namn: "Sara Nilsson",
    stad: "Vårgårda",
    bio: "Noggrann och snabb. Gillar ordning och att jobba med människor. Söker extraarbete.",
    taggar: ["Service", "Tempo", "Noggrann", "Kassa"],
  },
  {
    namn: "Leo Karlsson",
    stad: "Borlänge",
    bio: "Jag söker sommarjobb och kan jobba heltid. Jag är punktlig och gillar fysiskt arbete.",
    taggar: ["Lager", "Sommar", "Tempo", "Punktlig"],
  },
];

let profilIndex = 0;

function renderProfil(p) {
  document.getElementById("profilNamn").textContent = p.namn;
  document.getElementById("profilStad").textContent = p.stad;
  document.getElementById("profilBio").textContent = p.bio;

  const taggWrap = document.getElementById("profilTaggar");
  taggWrap.innerHTML = "";
  p.taggar.slice(0, 8).forEach((t) => {
    const s = document.createElement("span");
    s.className = "tagg";
    s.textContent = t;
    taggWrap.appendChild(s);
  });
}

function nextProfil() {
  profilIndex = (profilIndex + 1) % profiler.length;
  renderProfil(profiler[profilIndex]);
}

document.getElementById("knappNej")?.addEventListener("click", nextProfil);
document.getElementById("knappJa")?.addEventListener("click", nextProfil);

// init
renderProfil(profiler[profilIndex]);
