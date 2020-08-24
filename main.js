import "./scripts/main.js";
import "./styles/main.scss";

const tabsTemplate = `
<style>
.tabs-wrapper{
  background:#efefef;
}
  ::slotted([aria-hidden="true"]){
    display:none;
  }
  [aria-selected="true"]{
    background:black;
    color:white;
  }
</style>
<div class="tabs-wrapper">
  <div class="tabs">
    
  </div>
  <div class="content">
    <slot name="section"></slot>
  </div>
</div>
`;
class Tabs extends HTMLElement {
  constructor() {
    super();
    this.shadow = this.attachShadow({ mode: "open" });
    this.shadowRoot.innerHTML = tabsTemplate;

    this.tabSlot = this.shadow.querySelector(".tabs");
    this.sections = [...this.querySelectorAll(`tab-🚀`)];

    this.sections.forEach((section, index) => {
      section.slot = "section";
      const tabButton = this.createTabButton(section, index);
      this.tabSlot.appendChild(tabButton);
    });

    this.tabs = [...this.tabSlot.querySelectorAll("button")];
  }

  set selected(idx) {
    this.selectTab(idx);
  }

  connectedCallback() {
    this.selected = 0;
    this._handleTabClick = this.handleTabClick.bind(this);
    this.tabSlot.addEventListener("click", this._handleTabClick);
  }
  disconnectedCallback() {
    this.tabSlot.removeEventListener("click", this._handleTabClick);
  }

  createTabButton(section, id) {
    const button = document.createElement("button");
    button.setAttribute("data-panel", id);
    button.innerText = section.title || `panel ${id + 1}`;
    return button;
  }

  handleTabClick(e) {
    if (e.target.dataset.panel) {
      // console.log(this.tabs.findIndex);
      this.selected = this.tabs.findIndex((tab) => tab === e.target);
      e.target.focus();
    }
  }
  selectTab(idx) {
    for (let i = 0, tab; (tab = this.tabs[i]); ++i) {
      let select = i === idx;
      tab.setAttribute("tabindex", select ? 0 : -1);
      tab.setAttribute("aria-selected", select);
      this.sections[i].setAttribute("aria-hidden", !select);
    }
  }
}
window.customElements.define("tabs-🚀", Tabs);
window.customElements.define("tab-🚀", class extends HTMLElement {});
