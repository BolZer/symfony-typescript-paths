import {path_user_route, path_users_route_with_requirements_and_defaults} from "./output";

test('test path_user_route relative route', () => {
    const result1 = path_user_route().relative({id: "exampleID", noteId: "exampleNoteID"})
    expect(result1).toBe('/user/exampleID/notes/exampleNoteID');

    const result2 = path_user_route().relative({id: "exampleID", noteId: "exampleNoteID"}, {count: "20", page: "3"})
    expect(result2).toBe('/user/exampleID/notes/exampleNoteID?count=20&page=3');
});

test('test path_user_route absolute route', () => {
    const result1 = path_user_route().absolute({id: "exampleID", noteId: "exampleNoteID"})
    expect(result1).toBe('https://app.development.org/user/exampleID/notes/exampleNoteID');

    const result2 = path_user_route().absolute({id: "exampleID", noteId: "exampleNoteID"}, {count: "20", page: "3"})
    expect(result2).toBe('https://app.development.org/user/exampleID/notes/exampleNoteID?count=20&page=3');
});

test('test path_users_route_with_requirements_and_defaults - to assure that defaults and requirements are properly understood', () => {
    // Test if the default for locale takes effect
    const result1 = path_users_route_with_requirements_and_defaults().relative({id: 1})
    expect(result1).toBe('/users/1/en');

    // Test if the default for locale does not take effect if an argument is for the param is given
    const result2 = path_users_route_with_requirements_and_defaults().relative({id: 1, locale: "fr"})
    expect(result2).toBe('/users/1/fr');

    // Test if the default for locale takes effect
    const result3 = path_users_route_with_requirements_and_defaults().absolute({id: 1})
    expect(result3).toBe('https://app.development.org/users/1/en');

    // Test if the default for locale does not take effect if an argument is for the param is given
    const result4 = path_users_route_with_requirements_and_defaults().absolute({id: 1, locale: "fr"})
    expect(result4).toBe('https://app.development.org/users/1/fr');
})