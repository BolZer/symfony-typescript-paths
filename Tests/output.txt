type L = 'de'|'en'
const rRP = (rawRoute: string, routeParams: Record<string, string>): string => {Object.entries(routeParams).forEach(([key, value]) => rawRoute = rawRoute.replace(`{${key}}`, value)); return rawRoute;}
const aQP = (route: string, queryParams?: Record<string, string>): string => queryParams ? route + "?" + new URLSearchParams(queryParams).toString() : route;
export const path_test_route = (queryParams?: Record<string, string>): string => aQP('/test', queryParams);
export const path_user_route = (routeParams: {id: string, nodeID: string}, queryParams?: Record<string, string>): string => aQP(rRP('/user/{id}/{nodeID}', routeParams), queryParams);