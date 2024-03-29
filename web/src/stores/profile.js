import { defineStore } from "pinia";
import { apiClient, handleErrors, setAuthToken } from "src/boot/apiClient";

export const useProfileStore = defineStore("profile", {
  state: () => ({
    token: "",
    id: null,
    email: "",
    vehiclesCount: null,
    permissions: [],
    vehicles: [],
  }),
  getters: {
    hasPermission: (state) => {
      return (permissionName) => {
        const result = state.permissions?.find(
          (permission) => permission.name === permissionName
        );
        return !!result;
      };
    },
    getVehicles: (state) => {
      return state.vehicles.data;
    },
  },
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
            vehiclesCount
            permissions {
              id
              name
            }
          }
        }
      `;
      return await apiClient
        .executeQuery({
          query: ProfileQuery,
          cachePolicy: "network-only",
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          } else {
            this.id = response.data.me.id;
            this.email = response.data.me.email;
            this.permissions = response.data.me.permissions;
            this.vehiclesCount = response.data.me.vehiclesCount;
            return response;
          }
        });
    },
    async fetchVehicles() {
      const VehiclesQuery = `
        query {
          me {
            vehicles {
              data {
                id
                name
                fuel
                avgFuelConsumption
                priceSetting
                canAdd
                canEdit
                canDelete
                order
              }
              paginatorInfo {
                currentPage
                lastPage
              }
            }
          }
        }
      `;
      return await apiClient
        .executeQuery({
          query: VehiclesQuery,
          cachePolicy: "network-only",
        })
        .then((response) => {
          if (response.error) {
            handleErrors(response.error);
          } else {
            this.vehicles = response.data.me.vehicles;
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
    async updateVehicleOrder(vehicleId, newOrder) {
      const updateVehicleOrderMutation = `
        mutation updateVehicleOrder ($userId: ID! $vehicleId: ID!, $newOrder: Int!) {
          updateUser(
            id: $userId
            input: { vehicles: { connect: [{ id: $vehicleId, order: $newOrder }] } }
          ) {
            id
          }
        }
      `;
      const variables = {
        userId: this.id,
        vehicleId: vehicleId,
        newOrder: newOrder,
      };
      return await apiClient
        .executeMutation({
          query: updateVehicleOrderMutation,
          variables: variables,
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
