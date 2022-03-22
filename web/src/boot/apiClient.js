let token = "";

const setAuthToken = (newToken) => {
  token = newToken;
};

function authPlugin({ opContext }) {
  if (token) {
    opContext.headers.Authorization = `Bearer ${token}`;
  }
}

import { createClient, defaultPlugins } from "villus";
import { Notify } from "quasar";

const apiClient = createClient({
  url: "http://localhost:8000/graphql",
  use: [authPlugin, ...defaultPlugins()],
});

const handleErrors = (responseError, type = "negative") => {
  responseError.response.body.errors.forEach((error) => {
    Notify.create({
      type: type,
      message: error.message,
    });
  });
};

export default ({ app }) => {
  app.config.globalProperties.$apiClient = apiClient;
};

export { apiClient, handleErrors, setAuthToken };
