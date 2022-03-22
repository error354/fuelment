import { defineStore } from "pinia";
import { apiClient, handleErrors, setAuthToken } from "src/boot/apiClient";

export const useProfileStore = defineStore("profile", {
  state: () => ({
    token: "",
    data: {},
  }),
  getters: {},
  actions: {
    async login(email, password) {
      const LoginMutation = `
        mutation login ($email: String! $password: String! $device: String!) {
          login (email: $email, password: $password, device: $device)
        }
      `;
      const credentials = {
        email: email,
        password: password,
        device: "web",
      };
      return await apiClient
        .executeMutation({
          query: LoginMutation,
          variables: credentials,
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          } else {
            this.token = response.data.login;
            setAuthToken(this.token);
          }
          return response;
        });
    },
    async logout() {
      const LogoutMutation = `
        mutation {
          logout
        }
      `;
      return await apiClient
        .executeMutation({
          query: LogoutMutation,
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          } else {
            this.token = "";
            this.data = {};
            setAuthToken(this.token);
          }
          return response;
        });
    },
    async fetchProfile() {
      const ProfileQuery = `
        query {
          me {
            id
            email
            permissions {
              id
              name
            }
            vehicles {
              id
              name
              canAdd
              canEdit
              canDelete
              order
            }
          }
        }
      `;
      return await apiClient
        .executeQuery({
          query: ProfileQuery,
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          } else {
            this.data = response.data.me;
            return response;
          }
        });
    },
    async forgotPassword(email) {
      const ForgotPasswordMutation = `
        mutation forgotPassword ($email: String! $url: String!) {
          forgotPassword (email: $email, url: $url)
        }
      `;
      const variables = {
        email: email,
        url: "http://localhost:9000/reset-password?email=__EMAIL__&token=__TOKEN__",
      };
      return await apiClient
        .executeMutation({
          query: ForgotPasswordMutation,
          variables: variables,
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          return response;
        });
    },
    async resetPassword(email, token, password) {
      const ResetPasswordMutation = `
        mutation resetPassword ($email: String! $token: String!, $password: String!) {
          resetPassword (email: $email, token: $token, password: $password)
        }
      `;
      return await apiClient
        .executeMutation({
          query: ResetPasswordMutation,
          variables: {
            email: email,
            token: token,
            password: password,
          },
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          }
          return response;
        });
    },
  },

  persist: {
    paths: ["token"],
    afterRestore: (context) => {
      const token = context.store.token;
      setAuthToken(token);
    },
  },
});
