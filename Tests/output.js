"use strict";
exports.__esModule = true;
exports.path_user_route = exports.path_test_route = void 0;
var rRP = function (rawRoute, routeParams) { Object.entries(routeParams).forEach(function (_a) {
    var key = _a[0], value = _a[1];
    return rawRoute = rawRoute.replace("{" + key + "}", value);
}); return rawRoute; };
var aQP = function (route, queryParams) { return queryParams ? route + "?" + new URLSearchParams(queryParams).toString() : route; };
var path_test_route = function (queryParams) { return aQP('/test', queryParams); };
exports.path_test_route = path_test_route;
var path_user_route = function (routeParams, queryParams) { return aQP(rRP('/user/{id}/{nodeID}', routeParams), queryParams); };
exports.path_user_route = path_user_route;
