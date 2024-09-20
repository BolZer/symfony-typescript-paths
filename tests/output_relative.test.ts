import {path_user_route} from "./output_relative";

test('test path_user_route relative route', () => {
    const result1 = path_user_route().relative({id: "exampleID", noteId: "exampleNoteID"})
    expect(result1).toBe('/user/exampleID/notes/exampleNoteID');

    const result2 = path_user_route().relative({id: "exampleID", noteId: "exampleNoteID"}, {count: "20", page: "3"})
    expect(result2).toBe('/user/exampleID/notes/exampleNoteID?count=20&page=3');
});

test('test specific generation of only relative routes', () => {
    const routeObject = path_user_route();

    expect(routeObject).toHaveProperty("relative");
    expect(routeObject).not.toHaveProperty("absolute");
    expect(Object.keys(routeObject).length).toBe(1)
});