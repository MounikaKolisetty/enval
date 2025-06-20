import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EventspageSvaComponent } from './eventspage-sva.component';

describe('EventspageSvaComponent', () => {
  let component: EventspageSvaComponent;
  let fixture: ComponentFixture<EventspageSvaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EventspageSvaComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(EventspageSvaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
