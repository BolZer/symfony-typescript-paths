import {path_user_route} from "./output";

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