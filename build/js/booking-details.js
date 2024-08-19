import { handleSidebar, handleDisplayCurrentTime, handleNotification } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleSidebar();
  handleDisplayCurrentTime();
  handleNotification();
});


