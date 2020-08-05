//main.js
window.addEventListener("DOMContentLoaded", initSingleRecipeScraper);
function initSingleRecipeScraper() {
  if (!document.querySelector(".recipe-scraper-admin-page")) return;

  const form = document.querySelector(
    '.recipe-scraper-admin-page [data-tab="single-import"] form'
  );
  const button = form.querySelector("button");
  const idleButtonText = button.innerText;
  if (!form) return;

  form.addEventListener("submit", sendUrlToScrape);

  async function sendUrlToScrape(e) {
    e.preventDefault();
    const form = e.target;

    const recipe = form.querySelector("#recipe");
    const recipeVal = recipe.value;

    const youtube = form.querySelector("#youtube");
    const youtubeVal = youtube.value;

    const data = {
      action: "GFOAS_scrape",
      recipe: recipeVal,
      youtube: youtubeVal,
    };

    const body = toQueryString(data);
    button.innerText = "Loading.....";
    const res = await fetch(WP.ajax, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body,
    }).then((res) => res.json());
    button.innerText =
      res.message === "success"
        ? showSuccess(button, idleButtonText)
        : "Error, try again";
    recipe.value = "";
    youtube.value = "";
    if (res.message === "success") {
      const links = document.querySelector(".links");
      if (links.querySelector(".temp")) {
        links.removeChild(links.querySelector(".temp"));
      }
      const newLink = document.createElement("div");
      newLink.innerHTML = ` <a href="${res.link}" style="padding:10px 0; display:inline-block;">${res.link}</a>`;
      links.appendChild(newLink);
    }
  }
}

function showSuccess(el, idleText) {
  setTimeout(() => {
    el.innerText = idleText;
  }, 2000);
  return "success";
}

const toQueryString = (data) => {
  const urlSearhParams = new URLSearchParams(data);
  const queryString = urlSearhParams.toString();
  return queryString;
};
