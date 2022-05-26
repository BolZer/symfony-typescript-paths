const replaceRouteParams = (rawRoute: string, routeParams: Record<string, string|number|null>): string => {Object.entries(routeParams).forEach(([key, value]) => rawRoute = rawRoute.replace(`{${key}}`, value as string)); return rawRoute;}
const appendQueryParams = (route: string, queryParams?: Record<string, string|number>): string => queryParams ? route + "?" + new URLSearchParams(queryParams as Record<string, string>).toString() : route;
export const path_user_route = (): { relative: (routeParams: {id:string, noteId:string}, queryParams?: Record<string, string>) => string, } => {return {relative: (routeParams: {id:string, noteId:string}, queryParams?: Record<string, string>): string => appendQueryParams(replaceRouteParams('/user/{id}/notes/{noteId}', routeParams), queryParams), }};
export const path_user_route_http = (): { relative: (routeParams: {id:string, noteId:string}, queryParams?: Record<string, string>) => string, } => {return {relative: (routeParams: {id:string, noteId:string}, queryParams?: Record<string, string>): string => appendQueryParams(replaceRouteParams('/user/{id}/notes/{noteId}', routeParams), queryParams), }};
export const path_user_route_without_scheme = (): { relative: (routeParams: {id:string, noteId:string}, queryParams?: Record<string, string>) => string, } => {return {relative: (routeParams: {id:string, noteId:string}, queryParams?: Record<string, string>): string => appendQueryParams(replaceRouteParams('/user/{id}/notes/{noteId}', routeParams), queryParams), }};
export const path_users_route_without_requirements_and_defaults = (): { relative: (queryParams?: Record<string, string>) => string, } => {return {relative: (queryParams?: Record<string, string>): string => appendQueryParams('/users', queryParams), }};
export const path_users_route_with_requirements = (): { relative: (routeParams: {id:number, locale:'en'|'fr'}, queryParams?: Record<string, string>) => string, } => {return {relative: (routeParams: {id:number, locale:'en'|'fr'}, queryParams?: Record<string, string>): string => appendQueryParams(replaceRouteParams('/users/{id}/{locale}', routeParams), queryParams), }};
export const path_users_route_with_requirements_and_defaults = (): { relative: (routeParams: {id:number, locale?:'en'|'fr'}, queryParams?: Record<string, string>) => string, } => {return {relative: (routeParams: {id:number, locale?:'en'|'fr'}, queryParams?: Record<string, string>): string => appendQueryParams(replaceRouteParams('/users/{id}/{locale}', {...{"locale":"en"}, ...routeParams}), queryParams), }};
export const path_users_route_with_requirements_and_null_defaults = (): { relative: (routeParams: {id:number, locale?:'en'|'fr'}, queryParams?: Record<string, string>) => string, } => {return {relative: (routeParams: {id:number, locale?:'en'|'fr'}, queryParams?: Record<string, string>): string => appendQueryParams(replaceRouteParams('/users/{id}/{locale}', {...{"locale":null}, ...routeParams}), queryParams), }};