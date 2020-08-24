//main.js
window.addEventListener("DOMContentLoaded", initSingleRecipeScraper);
window.addEventListener("DOMContentLoaded", initCSVRecipeScraper);
function initSingleRecipeScraper() {
  if (!document.querySelector(".recipe-scraper-admin-page")) return;

  const form = document.querySelector(
    '.recipe-scraper-admin-page [data-tab="single-import"] form'
  );
  if (!form) return;
  const button = form.querySelector("button");
  const idleButtonText = button.innerText;

  form.addEventListener("submit", sendUrlToScrape);

  async function sendUrlToScrape(e) {
    e.preventDefault();
    //clears any errors
    const errorBlock = document.querySelector(".errors");
    errorBlock.innerHTML = "";

    const form = e.target;

    const recipe = form.querySelector("#recipe");
    const recipeVal = recipe.value;

    const youtube = form.querySelector("#youtube");
    const youtubeVal = youtube.value;

    const data = {
      action: "GFOAS_scrape_single",
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
    if (res.message === "success") {
      temporaryMessage(button, "success", idleButtonText);
    } else {
      temporaryMessage(button, "Error, Please Try Again", idleButtonText);
    }
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
    } else {
      const errorBlock = document.querySelector(".errors");

      const errorString = `<pre>${JSON.stringify(res.message, null, 2)}</pre>`;
      temporaryMessage(errorBlock, errorString, "", 1000000);
    }
  }
}

function initCSVRecipeScraper() {
  if (!document.querySelector(".recipe-scraper-admin-page")) return;

  const form = document.querySelector(
    '.recipe-scraper-admin-page [data-tab="csv-import"] form'
  );
  if (!form) return;
  const button = form.querySelector("button");
  const idleButtonText = button.innerText;
  form.addEventListener("submit", sendCSVImport);

  async function sendCSVImport(e) {
    e.preventDefault();

    const form = e.target;
    button.innerText = "Loading...";

    const csv = form.querySelector("[name='csvfile']").files[0];
    const formData = new FormData();
    formData.append("csv", csv);
    formData.append("action", "GFOAS_scrape_csv");
    // const body = toQueryString(data);
    // console.log(body);
    console.log(formData);
    const res = await fetch(WP.ajax, {
      method: "POST",
      credentials: "same-origin",
      body: formData,
    }).then((res) => res.json());

    console.log(res);
    temporaryMessage(button, "success", idleButtonText);
  }
}

function temporaryMessage(el, msg, idleText, time = 2000, html = false) {
  if (html) {
    el.innerHTML = msg;
  } else {
    el.innerHTML = msg;
  }
  setTimeout(() => {
    if (html) {
      el.innerHTML = idleText;
    } else {
      el.innerText = idleText;
    }
  }, time);
}

const toQueryString = (data) => {
  const urlSearhParams = new URLSearchParams(data);
  const queryString = urlSearhParams.toString();
  return queryString;
};
