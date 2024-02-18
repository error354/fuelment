import { useRouter, useRoute } from "vue-router";

export function useUrlHelper() {
  const router = useRouter();
  const route = useRoute();
  const setQueryParams = (params) => {
    router.replace({
      path: route.fullPath,
      query: {
        ...route.query,
        ...params,
      },
    });
  };

  return { setQueryParams };
}
