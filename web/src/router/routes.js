const routes = [
  {
    path: "/login",
    component: () => import("layouts/AuthLayout.vue"),
    children: [
      { path: "/login", component: () => import("pages/LoginPage.vue") },
      {
        path: "/forgot-password",
        component: () => import("pages/ForgotPasswordPage.vue"),
      },
      {
        path: "/reset-password",
        component: () => import("pages/ResetPasswordPage.vue"),
      },
    ],
  },

  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      {
        path: "",
        component: () => import("pages/IndexPageWrapper.vue"),
      },
      {
        path: "/vehicle/:id",
        component: () => import("pages/VehicleDetailsPage.vue"),
      },
    ],
  },

  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/ErrorNotFound.vue"),
  },
];

export default routes;
