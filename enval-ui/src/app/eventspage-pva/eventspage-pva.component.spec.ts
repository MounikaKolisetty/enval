import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EventspagePvaComponent } from './eventspage-pva.component';

describe('EventspagePvaComponent', () => {
  let component: EventspagePvaComponent;
  let fixture: ComponentFixture<EventspagePvaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EventspagePvaComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(EventspagePvaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
