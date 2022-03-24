import {path_user_route} from "./output_absolute";

test('test path_user_route absolute route', () => {
    const result1 = path_user_route().absolute({id: "exampleID", noteId: "exampleNoteID"})
    expect(result1).toBe('https://app.development.org/user/exampleID/notes/exampleNoteID');

    const result2 = path_user_route().absolute({id: "exampleID", noteId: "exampleNoteID"}, {count: "20", page: "3"})
    expect(result2).toBe('https://app.development.org/user/exampleID/notes/exampleNoteID?count=20&page=3');
});

test('test specific generation of only absolute routes', () => {
    const routeObject = path_user_route();
    expect(routeObject).toHaveProperty("absolute");
    expect(routeObject).not.toHaveProperty("relative");
    expect(Object.keys(routeObject).length).toBe(1)
});